<?php
///ipanel/controller/ticket/ticket_details.php
use ipanel\model\AdminModel;
use ipanel\model\CommentModel;
use ipanel\model\ManHourModel;
use ipanel\model\NotificationModel;
use ipanel\model\StructureModel;
use ipanel\model\TicketModel;
use iweb\model\UserModel;
use ICore\SmtpMailer;

// general class
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);
$textToolsClass = TextTools::getInstance();

// دسترسی غیرفعال‌سازی کامنت بر اساس لیست مدیران مجاز عملیات
$canDeactivateCommentByOperation =$rbacClass->getAdminsByOperationName('deactivation_operation') ;

// models
$userModel = new UserModel($db);
$adminModel = new AdminModel($db);
$ticketsModel = new TicketModel($db);
$commentModel = new CommentModel($db);
$manhourModel = new ManHourModel($db);
$structureModel = new StructureModel($db);
$notificationModel = new NotificationModel($db);



if (isset($_GET['ticket_id']))
    $ticket_id_encrypt = $_GET['ticket_id'];

$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));
$ticket_id = (int) $encryptorClass->decrypt($ticket_id_encrypt);



$part_name = 'tickets';
$commentModel->part_name = $part_name;
$commentModel->part_id = $ticket_id;

$manhourModel->part_name = $part_name;
$manhourModel->part_id = $ticket_id;

// view comment by user
if (isset($_GET['cid']) && $_GET['cid'] !== '') {
    $comment_id = intval($_GET['cid']);
    $notificationModel->setViewComments($comment_id);
}

// view ticket forward
if (isset($_GET['fid']) && $_GET['fid'] !== '') {
    $ticketId = intval($_GET['fid']);
    $notificationModel->setViewForwards($ticketId);
}



// user access
$is_entry = $rbacClass->checkPermissionOperationByName('pointer_operation') ? 1 : 0;
if ($ticketsModel->checkTicketAccess($ticket_id, $_SESSION['admin_id']) || $is_entry) {
    $ticketDetail = $ticketsModel->getTicketById($ticket_id);
    $ticketAllStatusHistory = $ticketsModel->getStatusHistory($ticket_id, 'tickets');
} else {
    echo "<script>window.location.replace('./');</script>";
    exit();

}



$uploadDir = '../irepository/tickets/';
$uploadDirDb = './irepository/tickets/';

$fileManager = new FileManager($db, $uploadDir);
$dbHandler = new DatabaseHandler($db);
$allFileData = $fileManager->getFileManageByPart($ticket_id, $part_name);



// accounting file
$uploadDirAccounting = '../irepository/accounting/';
$uploadDirAccountingDb = './irepository/accounting/';

$accountingFileManager = new FileManager($db, $uploadDirAccounting);
$allAccountingFileData = $accountingFileManager->getFileManageByPart($ticket_id, 'accounting');



if ($rbacClass->checkPermissionOperationByName('view_indicator_number_operation')) {
    $permissionViewIndicatorNumbert = true;
} else {
    $permissionViewIndicatorNumbert = false;
}

// all kanban tag
$allKanbanTag = $ticketsModel->getAllKabanTag();

$types = $structureModel->getTypesGroupedByTypeGroup();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    function safeSendForwardTicketEmail($config, $adminModel, $receiverAdminId, $ticketDetail)
    {
        try {
            $adminEmail = $adminModel->getAdminEmailById($receiverAdminId);

            if (empty($adminEmail['email'])) {
                return false;
            }

            $mailer = new SmtpMailer(
                $config->getConfig('smtpHost'),
                $config->getConfig('smtpUsername'),
                $config->getConfig('smtpPassword'),
                $config->getConfig('smtpPort2'),
                $config->getConfig('smtpSecure1')
            );

            if (method_exists($mailer, 'setTimeout')) {
                $mailer->setTimeout(5);
            }

            $ticketNumber = $ticketDetail['ticket_number'] ?? '';
            $ticketTitle = $ticketDetail['ticket_title'] ?? '';
            $companyName = $ticketDetail['company_name'] ?? '';

            $to = $adminEmail['email'];
            $subject = _lang['ticket'] . ' #' . $ticketNumber;

            $body = "
        <div style='font-family:Tahoma,Arial,sans-serif;font-size:14px;line-height:1.8;color:#333'>
            <h3 style='color:#0d6efd;margin-bottom:15px'>
                " . _lang['dear_admin'] . "
            </h3>

            <p>
                " . _lang['forward_ticket_email_body1'] . "
            </p>

            <div style='background:#f8f9fa;border:1px solid #dee2e6;padding:12px;border-radius:6px;margin:15px 0'>
                <strong>" . htmlspecialchars($ticketTitle, ENT_QUOTES, 'UTF-8') . "</strong>
                <br>
                <span>#" . htmlspecialchars($ticketNumber, ENT_QUOTES, 'UTF-8') . "</span>
                <br>
                <span>" . htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') . "</span>
            </div>

            <p>جهت بررسی و پیگیری تیکت، لطفاً وارد پنل شوید:</p>

            <p>
                <a href='https://intek.ir/idesk/ipanel'
                   target='_blank'
                   style='background:#0d6efd;color:#fff;padding:10px 18px;text-decoration:none;border-radius:5px;display:inline-block'>
                    " . _lang['enter_panel'] . "
                </a>
            </p>

            <hr style='border:none;border-top:1px solid #e5e5e5;margin-top:20px'>

            <small style='color:#777'>
                این ایمیل به صورت خودکار توسط سامانه پشتیبانی ارسال شده است.
            </small>
        </div>";

            $mailer->sendMail($to, $subject, $body);

            return true;

        } catch (Throwable $e) {
            error_log('Forward ticket email send failed: ' . $e->getMessage());
            return false;
        }
    }


    // forward
    if (isset($_POST['forward'])) {

        $table_set = 'forwards';

        $arrreceiver_person_id_a = $_POST['receiver_person_id_a'] ?? null;
        $forwards_description = $_POST['forwards_description'] ?? '';
        $sender_signature = !empty($_POST['sender_signature']) ? 1 : 0;

        $sender_person_id = $_SESSION['admin_id'];
        $sender_rbac_id = $_SESSION['rbac_id'];
        $sender_type = 'a';

        $section_element_id_encrypt = $_POST['section_element_id'];
        $section_element_id = (int) $encryptorClass->decrypt($section_element_id_encrypt);

        $section_part_name = $_POST['section_part_name'];
        $intReferred = $_POST['referred'] ?? null;
        $strConditionName = $_POST['condition_name'] ?? null;

        $delivery_time = $_POST['delivery_time'] ?? null;
        $person_hour = $_POST['person_hour'] ?? null;

        $emailReceivers = [];

        if (is_array($arrreceiver_person_id_a) && !empty($arrreceiver_person_id_a)) {

            foreach ($arrreceiver_person_id_a as $receiver_person_id_a) {

                $commonData = [
                    'receiver_person_id' => $receiver_person_id_a,
                    'receiver_type' => 'a',
                    'sender_person_id' => $sender_person_id,
                    'sender_rbac_id' => $sender_rbac_id,
                    'sender_type' => $sender_type,
                    'section_element_id' => $section_element_id,
                    'section_part_name' => $section_part_name,
                ];

                $forwardData = array_merge($commonData, [
                    'forwards_description' => $forwards_description,
                    'sender_signature' => $sender_signature
                ]);

                $dbHandler->insertData($table_set, $forwardData);

                if (!empty($person_hour)) {
                    $personHourData = array_merge($commonData, [
                        'request' => 'person_hour'
                    ]);
                    $dbHandler->insertData('requests', $personHourData);
                }

                if (!empty($delivery_time)) {
                    $deliveryTimeData = array_merge($commonData, [
                        'request' => 'delivery_time'
                    ]);
                    $dbHandler->insertData('requests', $deliveryTimeData);
                }

                $emailReceivers[] = $receiver_person_id_a;
            }

            header("Location: ./");
            /*
                        if (function_exists('fastcgi_finish_request')) {
                            fastcgi_finish_request();
                        }

                        ignore_user_abort(true);

                        if (!empty($emailReceivers) && isset($ticketDetail)) {
                            foreach ($emailReceivers as $receiverId) {
                                safeSendForwardTicketEmail($config, $adminModel, $receiverId, $ticketDetail);
                            }
                        }*/

            exit;
        }
    }
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
        $dbHandler->updateData("tickets", $arrData, $whereCondition);
        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }

    // mark
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


    // change type
    if (isset($_POST['submitTypeChange'])) {


        $type_id = $_POST['type_id'];

        $ticket_id_encrypt = $_POST['ticket_id'];
        $ticket_id = (int) $encryptorClass->decrypt($ticket_id_encrypt);


        $arrData = [
            'type_id' => $type_id
        ];

        $whereCondition = 'id = ' . $ticket_id;
        $dbHandler->updateData("tickets", $arrData, $whereCondition);
        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }

    // attach file

    if (isset($_FILES['attach_file']) && ($_FILES['attach_file']['size']) > 0) {
        $uploadedFile = $fileManager->uploadFile($_FILES['attach_file']);
        $file_title = $_POST['file_title'];

        if (isset($_POST['global']) && $_POST['global'] == 'on') {
            $local = 0;
        } else {
            $local = 1;
        }

        $table_set = 'file_manage';
        $arrData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirDb,
            'file_title' => $file_title,
            'admin_id' => $_SESSION['admin_id'],
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

        if (isset($_POST['global']) && $_POST['global'] == 'on') {
            $local = 0;
        } else {
            $local = 1;
        }

        $table_set = 'file_manage';
        $arrData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirAccountingDb,
            'file_title' => $file_title,
            'admin_id' => $_SESSION['admin_id'],
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


    // غیرفعال‌سازی نرم کامنت
    // Flow:
    // 1) شناسه کامنت فقط از POST دریافت و عددی‌سازی می‌شود.
    // 2) رکورد از دیتابیس خوانده می‌شود تا مالکیت و تعلق آن به همین تیکت کنترل شود.
    // 3) فقط سازنده کامنت یا مدیر دارای deactivation_operation اجازه تغییر is_active را دارد.
    // 4) رکورد حذف نمی‌شود و فقط is_active = 0 می‌گردد.
    if (isset($_POST['deactivate_comment'])) {
        $commentId = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;

        if ($commentId > 0) {
            $commentToDeactivate = $ticketsModel->getCommentById($commentId);

            $commentBelongsToTicket =
                !empty($commentToDeactivate) &&
                (int) $commentToDeactivate['part_id'] === (int) $ticket_id &&
                $commentToDeactivate['part_name'] === $part_name;

            $isCommentCreator =
                $commentBelongsToTicket &&
                !empty($commentToDeactivate['admin_id']) &&
                (int) $commentToDeactivate['admin_id'] === (int) $_SESSION['admin_id'];

            if ($commentBelongsToTicket && ($isCommentCreator || $canDeactivateCommentByOperation)) {
                $ticketsModel->deactivateComment($commentId, $ticket_id);
            }
        }

        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }

    // comment
    if (isset($_POST['comment_text']) && !empty($_POST['comment_text'])) {

        $comment_text = $_POST['comment_text'];
        $parent_id = $_POST['parent_id'] ?? null;
        $creator_id = $_POST['creator_id'] ?? null;

        $dbHandler = new DatabaseHandler($db);

        if (isset($_POST['global']) && $_POST['global'] == 'on') {
            $local = 0;
        } else {
            $local = 1;
        }

        $table_set = 'comments';

        $arrData = [
            'comment_text' => $comment_text,
            'part_name' => $part_name,
            'part_id' => $ticket_id,
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

        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;
    }


    // man-hour set


    if (isset($_POST['hourSubmit']) && $_POST['man_hour'] > -1) {

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
                'part_id' => $ticket_id,
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

            header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
            exit;
        }
    } else {
        header("Refresh:0; url=tickets?ticket_id=" . $ticket_id_encrypt);
        exit;

    }
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


// marks list
$adminId = $_SESSION['admin_id'];
$companyId = $_SESSION['company_id'];
$markListsResult = $structureModel->getAdminMarkingTags($adminId, $companyId);

// todo list
$todoListsResult = $structureModel->getTodoLists();

// last status description if exist
$lastStatusDescription = $structureModel->getLastConditionDescription($ticket_id, $part_name);
$allStatusDescription = $structureModel->getAllConditionDescription($ticket_id, $part_name, 1);
$lastForwardDescription = $structureModel->getLastForwardDescription($ticket_id, $part_name);

$allForwardDescription = $structureModel->getAllForwardDescription($ticket_id, $part_name, 0, 1);

// accounting
$array_status = ['condition_acepted_test', 'condition_clearing', 'condition_invoice', 'condition_acepted_invoice', 'condition_official_bill'];
$showAccounting = $ticketsModel->checkStatusTableStatusArray($ticket_id, $part_name, $array_status);


//requests
$response_person_hour = $ticketsModel->getRequestsById($ticket_id, $part_name, 'person_hour');
$response_delivery_time = $ticketsModel->getRequestsById($ticket_id, $part_name, 'delivery_time');




if (!empty($ticketDetail)) {
    $ticketsModel->insertViewBy($ticket_id);
    $condition = $structureModel->getConditionsByName($ticketDetail['status']);

    if ($is_entry) {
        $ticketsModel->changeStatusIsEntry($ticket_id, "condition_under_review");

    } else {
        $ticketsModel->changeStatusIsNotEntry($ticket_id, "condition_in_progress");

    }

}


$allViewBy = $ticketsModel->getViewBy($ticket_id);







$allSchedule = $ticketsModel->getSchedule('tickets', $ticket_id);

$allConditions = $structureModel->getConditionsByPart($part_name);
$allScheduleConditions = $structureModel->getConditionsByPart('schedule');

if (!empty($ticketDetail['admin_id'])) {
    $userProfile = $adminModel->getAdminImageById($ticketDetail['admin_id']);
} else {
    $userProfile = $userModel->getUserImageById($ticketDetail['user_id']);
}



function getValue($fieldName, $ticketDetail)
{
    return isset($ticketDetail[$fieldName]) ? $ticketDetail[$fieldName] : '';
}


$allComments = $commentModel->getCommentPart();

$allManHour = $manhourModel->getManHourPart();
// role insert man hour
$isEntry = $rbacClass->checkPermissionOperationByName('pointer_operation') ? 1 : 0;
$setManhourTimeInsert = false;
if ($manhourModel->getLastManHourByAdminId($_SESSION['admin_id']) == $_SESSION['admin_id'] || $isEntry) {
    $setManhourTimeInsert = true;
}




$allFileInfo = array();
if ($allFileData && $allFileData->num_rows > 0) {
    while ($fileData = $allFileData->fetch_assoc()) {

        $allFileInfo[] = $fileManager->getFileInfoFromPath(
            "." . $fileData['file_path'] . $fileData['file_name'],
            $fileData['file_path'],
            $fileData['file_title'],
            '',
            $fileData['local'],
            $fileData['user_id'],
            $fileData['creation_date']
        );
    }
}

if (isset($_GET['file']) && !empty($_GET['file'])) {
    $fileManager->fileDownload($_GET['file']);
}






// accounting file

$allAccountingFileInfo = array();
if ($allAccountingFileData && $allAccountingFileData->num_rows > 0) {
    while ($accountingFileData = $allAccountingFileData->fetch_assoc()) {

        $allAccountingFileInfo[] = $accountingFileManager->getFileInfoFromPath("." . $accountingFileData['file_path'] . $accountingFileData['file_name'], $accountingFileData['file_path'], $accountingFileData['file_title'], '', $accountingFileData['local'], $accountingFileData['user_id']);
    }
}

if (isset($_GET['accounting_file']) && !empty($_GET['accounting_file'])) {
    $accountingFileManager->fileDownload($_GET['accounting_file']);
}




