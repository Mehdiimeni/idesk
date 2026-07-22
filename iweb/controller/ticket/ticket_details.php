<?php
///iweb/controller/ticket/tickets_details.php
use iweb\model\NotificationModel;
use iweb\model\CommentModel;
use iweb\model\StructureModel;
use iweb\model\TicketModel;
use iweb\model\UserModel;

use ipanel\model\AdminModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
$adminModel = new AdminModel($db);
$ticketsModel = new TicketModel($db);
$commentModel = new CommentModel($db);
$rbacClass = new RBAC($db);
$structureModel = new StructureModel($db);

$textToolsClass = TextTools::getInstance();
$notificationModel = new NotificationModel($db);

if (isset($_GET['ticket_id']))
    $ticket_id_encrypt = $_GET['ticket_id'];

$encryptorClass = new Encryptor($config->getConfig('encryptWebKey'));
$ticket_id = (int) $encryptorClass->decrypt($ticket_id_encrypt);

$part_name = 'tickets';
$commentModel->part_name = $part_name;
$commentModel->part_id = $ticket_id;
$person_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];




// user access
$ticketDetail = $ticketsModel->getTicketById($ticket_id)->fetch_assoc();


// Ticket condition control
$changeCondition = $ticketsModel->checkLastStatusMatch($ticket_id, $part_name, $person_id, $company_id);
$excludedStatuses = [
    'condition_in_progress',
    'condition_under_review',
    'condition_acepted_invoice_auto',
    'condition_acepted_test_auto',
    'condition_pendency',
    'condition_official_bill',
    'condition_clearing',
    'condition_reject_test',
    'condition_archive',
    'condition_pending',
    'condition_acepted_test',
    'condition_regect',
    'condition_duplicate',
    'condition_final_done',
];



if (in_array(strtolower($ticketDetail['status'] ?? ''), $excludedStatuses)) {
    $changeCondition = false;
}


// show priority

$showPriority = '';
$priority = $ticketDetail['priority'];
if ($priority == 'low') {
    $showPriority = '<div class="ribbon ribbon-primary float-start"> ' . _lang[$priority] . ' </div>';
}
if ($priority == 'medium') {
    $showPriority = '<div class="ribbon ribbon-warning float-start"> ' . _lang[$priority] . ' </div>';
}
if ($priority == 'high') {
    $showPriority = '<div class="ribbon ribbon-danger float-start"> ' . _lang[$priority] . ' </div>';
}


// accounting
$array_status = ['condition_acepted_test', 'condition_clearing', 'condition_invoice', 'condition_acepted_invoice', 'condition_official_bill'];
$showAccounting = $ticketsModel->checkStatusTableStatusArray($ticket_id, $part_name, $array_status);



//  just for this company
if ($ticketDetail['company_id'] != $_SESSION["company_id"] and $ticketDetail['admin_id'] == null) {
    echo "<script>window.location.replace('./');</script>";
    exit();
}


// view comment by user
if (isset($_GET['cid']) && $_GET['cid'] !== '') {
    $comment_id = intval($_GET['cid']);
    if (!empty($ticketDetail))
        $notificationModel->setViewComments($comment_id);
}

if (isset($_GET['rgid']) && $_GET['rgid'] !== '') {
    $description_id = intval($_GET['rgid']);
    if (!empty($ticketDetail))
        $notificationModel->setViewStatusUser($description_id);
}


$allSchedule = $ticketsModel->getSchedule('tickets', $ticket_id);

$allConditions = $structureModel->getConditionsByPart($part_name);
$allScheduleConditions = $structureModel->getConditionsByPart('schedule');



function getValue($fieldName, $ticketDetail)
{
    return isset($ticketDetail[$fieldName]) ? $ticketDetail[$fieldName] : '';
}


// if send from admin
if (!empty($ticketDetail['admin_id'])) {
    $userProfile = $adminModel->getAdminImageById($ticketDetail['admin_id']);
} else {
    $userProfile = $userModel->getUserImageById($ticketDetail['user_id']);
}

$allComments = $commentModel->getCommentPart();



$uploadDir = './irepository/tickets/';
$uploadDirDb = './irepository/tickets/';
$fileManager = new FileManager($db, $uploadDir);

$dbHandler = new DatabaseHandler($db);

$allFileData = $fileManager->getFileManageByPart($ticket_id, $part_name);

$allFileInfo = array();
if ($allFileData && $allFileData->num_rows > 0) {
    while ($fileData = $allFileData->fetch_assoc()) {

        $allFileInfo[] = $fileManager->getFileInfoFromPath($fileData['file_path'] . $fileData['file_name'], $fileData['file_path'], $fileData['file_title']);
    }
}


if (isset($_GET['file']) && !empty($_GET['file'])) {
    $fileManager->fileDownload($_GET['file']);
}


// accounting file
$uploadDirAccounting = './irepository/accounting/';
$uploadDirAccountingDb = './irepository/accounting/';

$accountingFileManager = new FileManager($db, $uploadDirAccounting);
$allAccountingFileData = $accountingFileManager->getFileManageByPart($ticket_id, 'accounting');

$allAccountingFileInfo = array();
if ($allAccountingFileData && $allAccountingFileData->num_rows > 0) {
    while ($accountingFileData = $allAccountingFileData->fetch_assoc()) {

        $allAccountingFileInfo[] = $accountingFileManager->getFileInfoFromPath($accountingFileData['file_path'] . $accountingFileData['file_name'], $accountingFileData['file_path'], $accountingFileData['file_title']);
    }
}

if (isset($_GET['accounting_file']) && !empty($_GET['accounting_file'])) {
    $accountingFileManager->fileDownload($_GET['accounting_file']);
}


// all kanban tag
$allKanbanTag = $ticketsModel->getAllKabanTag();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    // attach file

    if (isset($_FILES['attach_file']) && ($_FILES['attach_file']['size']) > 0) {
        $uploadedFile = $fileManager->uploadFile($_FILES['attach_file']);
        $file_title = $_POST['file_title'];

        if (isset($_POST['local']) && $_POST['local'] == 'on') {
            $local = 1;
        } else {
            $local = 0;
        }

        $table_set = 'file_manage';
        $arrData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirDb,
            'file_title' => $file_title,
            'user_id' => $_SESSION['user_id'],
            'part_id' => $ticketDetail['id'],
            'part_name' => $part_name,
            'local' => $local,
            'company_id' => $_SESSION['company_id']

        ];

        $unique_fields = [
            ''
        ];


        $dbHandler->insertData($table_set, $arrData);

        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }


    // attach accounting file

    if (isset($_FILES['accounting_attach_file']) && ($_FILES['accounting_attach_file']['size']) > 0) {
        $uploadedFile = $accountingFileManager->uploadFile($_FILES['accounting_attach_file']);
        $file_title = $_POST['accounting_file_title'];

        if (isset($_POST['local']) && $_POST['local'] == 'on') {
            $local = 1;
        } else {
            $local = 0;
        }

        $table_set = 'file_manage';
        $arrData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirAccountingDb,
            'file_title' => $file_title,
            'user_id' => $_SESSION['user_id'],
            'part_id' => $ticketDetail['id'],
            'part_name' => 'accounting',
            'local' => $local,
            'company_id' => $_SESSION['company_id']

        ];

        $unique_fields = [
            ''
        ];


        $dbHandler->insertData($table_set, $arrData);

        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }


    // comment
    if (isset($_POST['comment_text']) && !empty($_POST['comment_text'])) {

        $comment_text = $_POST['comment_text'];
        $parent_id = $_POST['parent_id'] ?? null;
        $creator_id = $_POST['creator_id'] ?? null;

        $dbHandler = new DatabaseHandler($db);

        if (isset($_POST['local']) && $_POST['local'] == 'on') {
            $local = 1;
        } else {
            $local = 0;
        }

        $table_set = 'comments';

        $arrData = [
            'comment_text' => $comment_text,
            'part_name' => $part_name,
            'part_id' => $ticket_id,
            'user_id' => $_SESSION['user_id'],
            'local' => $local,
            'company_id' => $_SESSION['company_id']
        ];

        if ($parent_id != null) {
            $arrData['parent_id'] = $parent_id;
            $arrData['creator_id'] = $creator_id;
        }



        $unique_fields = [];


        $insertResult = $dbHandler->insertData($table_set, $arrData);
        $message = $insertResult['message'];
        $insert_id = $insertResult['insert_id'];

        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }

    // Kaban board

    if (isset($_POST['kanban_board'])) {

        $table_set = 'kanban_board';

        $board_tag_id = $_POST['board_tag_id'];
        $description = $_POST['description'];
        $part_id_encrypt = $_POST['part_id'];
        $part_id = (int) $encryptorClass->decrypt($part_id_encrypt);
        $part_name = $_POST['part_name'];


        $arrData = [
            'board_tag_id' => $board_tag_id,
            'description' => $description,
            'part_id' => $part_id,
            'part_name' => $part_name
        ];

        $dbHandler->insertData($table_set, $arrData);
        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }


}



// marks list
$userId = $_SESSION['user_id'];
$companyId = $_SESSION['company_id'];
$markListsResult = $structureModel->getUserMarkingTags($userId, $companyId);
if (isset($_POST['submitMark'])) {


    $marking_tag_id = $_POST['marking_tag_id'];
    $part_id_encrypt = $_POST['part_id'];
    $part_id = (int) $encryptorClass->decrypt($part_id_encrypt);
    $part_name = $_POST['part_name'];

    if ($marking_tag_id > 0) {

        $arrData = [
            'marking_tag_id' => $marking_tag_id,
            'part_id' => $part_id,
            'part_name' => $part_name,
        ];

        $dbHandler->insertData("marking", $arrData);
    } else {
        $marking_before_id = $_POST['marking_before_id'];
        if ($marking_before_id != null) {

            $whereCondition = 'marking_tag_id = ' . $marking_before_id . ' AND  part_id=' . $part_id . ' AND part_name="' . $part_name . '"';
            $dbHandler->deleteData("marking", $whereCondition);

        }
    }

    header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
    exit;
}