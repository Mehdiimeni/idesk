<?php
///controller/project/projects_details.php
use ipanel\model\AdminModel;
use ipanel\model\CommentModel;
use ipanel\model\ManHourModel;
use ipanel\model\NotificationModel;
use ipanel\model\ProjectManagerModel;
use ipanel\model\StructureModel;

// class
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$textToolsClass = TextTools::getInstance();
$notificationModel = new NotificationModel($db);
$rbacClass = new RBAC($db);

// model
$adminModel = new AdminModel($db);
$commentModel = new CommentModel($db);
$manhourModel = new ManHourModel($db);
$structureModel = new StructureModel($db);
$projectsModel = new ProjectManagerModel($db);

// get url data

if (isset($_GET['id']))
    $id_encrypt = $_GET['id'];

$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));
$projectId = (int) $encryptorClass->decrypt($id_encrypt);

$part_name = 'projects';
$commentModel->part_name = $part_name;
$commentModel->part_id = $projectId;

$manhourModel->part_name = $part_name;
$manhourModel->part_id = $projectId;


// member access
$isProjectAdmin = $projectsModel->cheakProjectAdmin($projectId);
if ($isProjectAdmin || $projectsModel->checkMemberInProject($projectId)) {
    $projectDetails = $projectsModel->getProjectById($projectId);
} else {
    echo "<script>window.location.replace('./');</script>";
    exit();

}

// show priority
$showPriority = '';
$priority = $projectDetails['priority'];
if ($priority == 'low') {
    $showPriority = '<div class="ribbon ribbon-primary float-start"> ' . _lang[$priority] . ' </div>';
}
if ($priority == 'medium') {
    $showPriority = '<div class="ribbon ribbon-warning float-start"> ' . _lang[$priority] . ' </div>';
}
if ($priority == 'high') {
    $showPriority = '<div class="ribbon ribbon-danger float-start"> ' . _lang[$priority] . ' </div>';
}



//requests
/*
$response_person_hour = $projects->getRequestsById($id, $part_name, 'person_hour');
$response_delivery_time = $projects->getRequestsById($id, $part_name, 'delivery_time');

*/

if (isset($_GET['cid']) && $_GET['cid'] !== '') {
    $comment_id = intval($_GET['cid']);
    if (!empty($projectDetails))
        $notificationModel->setViewComments($comment_id);
}

// schedule
$allSchedule = $projectsModel->getSchedule($projectId);
$allScheduleConditions = $structureModel->getConditionsByPart('schedule');

$allComments = $commentModel->getCommentPart();
$allManHour = $manhourModel->getManHourPart();

// role insert man hour
$setManhourTimeInsert = false;
if($manhourModel->getLastManHourByAdminId($_SESSION['admin_id']) == $_SESSION['admin_id']){
    $setManhourTimeInsert = true;
}


$uploadDir = '../irepository/projects/';
$uploadDirDb = './irepository/projects/';

$fileManager = new FileManager($db, $uploadDir);
$dbHandler = new DatabaseHandler($db);
$allFileData = $fileManager->getFileManageByPart($projectId, $part_name);

$allFileInfo = array();
if ($allFileData && $allFileData->num_rows > 0) {
    while ($fileData = $allFileData->fetch_assoc()) {

        $allFileInfo[] = $fileManager->getFileInfoFromPath("." . $fileData['file_path'] . $fileData['file_name'], $fileData['file_path'], $fileData['file_title']);
    }
}

if (isset($_GET['file']) && !empty($_GET['file'])) {
    $fileManager->fileDownload($_GET['file']);
}


// todo
$todoListsResult = $structureModel->getTodoLists();





if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // schedule
    if (isset($_POST['schedule'])) {

        $table_set = 'schedule';

        $date_time = $_POST['date_time'];
        $description = $_POST['description'];

        $admin_id = $_SESSION['admin_id'];
        $rbac_id = $_SESSION['rbac_id'];
        $user_type = 'a';
        $section_element_id_encrypt = $_POST['section_element_id'];
        $section_element_id = (int) $encryptorClass->decrypt($section_element_id_encrypt);
        $section_part_name = $_POST['section_part_name'];


        $arrData = [
            'date_time' => $date_time,
            'description' => $description,
            'admin_id' => $admin_id,
            'rbac_id' => $rbac_id,
            'user_type' => $user_type,
            'section_element_id' => $section_element_id,
            'section_part_name' => $section_part_name
        ];

        $dbHandler->insertData($table_set, $arrData);
        header("Refresh:0; url=projects?id=" . $id_encrypt);
        exit;
    }


    // Kaban board
    if (isset($_POST['kanban_board'])) {

        $table_set = 'kanban_board';

        $board_tag = $_POST['board_tag'];
        $description = $_POST['description'];

        $admin_id = $_SESSION['admin_id'];
        $rbac_id = $_SESSION['rbac_id'];
        $user_type = 'a';
        $part_id_encrypt = $_POST['part_id'];
        $part_id = (int) $encryptorClass->decrypt($part_id_encrypt);
        $part_name = $_POST['part_name'];


        $arrData = [
            'board_tag' => $board_tag,
            'description' => $description,
            'admin_id' => $admin_id,
            'rbac_id' => $rbac_id,
            'user_type' => $user_type,
            'part_id' => $part_id,
            'part_name' => $part_name
        ];

        $dbHandler->insertData($table_set, $arrData);
        header("Refresh:0; url=projects?id=" . $id_encrypt);
        exit;
    }

    // priority
    if (isset($_POST['submitPriority'])) {


        $priority = $_POST['priority'];

        $admin_id = $_SESSION['admin_id'];
        $rbac_id = $_SESSION['rbac_id'];
        $user_type = 'a';
        $part_id_encrypt = $_POST['part_id'];
        $part_id = (int) $encryptorClass->decrypt($part_id_encrypt);
        $part_name = $_POST['part_name'];


        $arrData = [
            'priority' => $priority,
            'admin_id' => $admin_id,
            'rbac_id' => $rbac_id,
            'part_id' => $part_id,
            'part_name' => $part_name
        ];

        $dbHandler->insertData("priority_status", $arrData);


        $arrData = [
            'priority' => $priority
        ];

        $whereCondition = 'id = ' . $part_id;
        $dbHandler->updateData("projects", $arrData, $whereCondition);
        header("Refresh:0; url=projects?id=" . $id_encrypt);
        exit;
    }

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
            'admin_id' => $_SESSION['admin_id'],
            'part_id' => $projectDetails['id'],
            'part_name' => $part_name,
            'local' => $local,
            'company_id' => $_SESSION['company_id']

        ];

        $unique_fields = [
            ''
        ];


        $dbHandler->insertData($table_set, $arrData);

        header("Refresh:0; url=projects?id=" . $id_encrypt);
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
            'part_id' => $projectId,
            'admin_id' => $_SESSION['admin_id'],
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

        header("Refresh:0; url=projects?id=" . $id_encrypt);
        exit;
    }


    // man-hour set

    if (isset($_POST['hourSubmit']) && !empty($_POST['man_hour'])) {

        if (isset($_POST['selected_todo_list']) && $_POST['selected_todo_list'] != '') {
            $man_hour = (int) $_POST['man_hour'];
            $parent_id = $_POST['parent_id'] ?? null;



            $dbHandler = new DatabaseHandler($db);


            $table_set = 'man_hour';

            $arrData = [
                'man_hour_number' => $man_hour,
                'todo' => $_POST['selected_todo_list'],
                'subject' => $_POST['subject'],
                'part_name' => $part_name,
                'part_id' => $projectId,
                'admin_id' => $_SESSION['admin_id']
            ];


            if ($parent_id != null)
                $arrData['parent_id'] = $parent_id;

            if (isset($_POST['local']) && !empty($_POST['local']))
                $arrData['company_id'] = $structureModel->getCompanyByUnitId($_SESSION['unit_id']);

            $unique_fields = [];


            $insertResult = $dbHandler->insertData($table_set, $arrData);
            $message = $insertResult['message'];
            $insert_id = $insertResult['insert_id'];

            header("Refresh:0; url=projects?id=" . $id_encrypt);
            exit;
        }
    } else {
        header("Refresh:0; url=projects?id=" . $id_encrypt);
        exit;

    }
}


