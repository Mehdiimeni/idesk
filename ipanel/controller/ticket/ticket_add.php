<?php
///controller/ticket/ticket_add.php
use ipanel\model\TicketModel;
use ipanel\model\StructureModel;
use ICore\SmtpMailer;
use ipanel\model\AdminModel;



$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$dbHandler = new DatabaseHandler($db);
$structureModel = new StructureModel($db);
$rbacClass = new RBAC($db);
$ticketsModel = new TicketModel($db);
$adminModel = new AdminModel($db);


$types = $structureModel->getTypesGroupedByTypeGroup();

$company_profilesResult = $structureModel->getCompanies();



function safeSendTicketEmail($config, $adminModel, $member, $ticketNumber, $title)
{
    try {
        
        $adminEmail = $adminModel->getAdminEmailById($member);

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

        // اگر کلاس SmtpMailer شما متد timeout داشته باشد
        if (method_exists($mailer, 'setTimeout')) {
            $mailer->setTimeout(5);
        }

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
                <strong>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</strong>
                <br>
                <span>#" . htmlspecialchars($ticketNumber, ENT_QUOTES, 'UTF-8') . "</span>
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
        error_log('Ticket email send failed: ' . $e->getMessage());
        return false;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['ticket_title']) || trim($_POST['ticket_title']) === '') {
        header("Location: ./tickets");
        exit;
    }

    $title = $_POST['ticket_title'];
    $description = $_POST['ticket_description'];
    $type_id = $_POST['type_id'];
    $company_id = $_POST['company_id'];
    $indicator_number = $_POST['indicator_number'];
    $priority = $_POST['priority'];
    $adminId = $_SESSION['admin_id'];
    $rbacId = $_SESSION['rbac_id'];
    $members = isset($_POST['members']) ? $_POST['members'] : null;

    $part_name = 'tickets';
    $table_set = 'tickets';

    $arrData = [
        'ticket_title' => $title,
        'ticket_description' => $description,
        'type_id' => $type_id,
        'priority' => $priority,
        'admin_id' => $adminId,
        'company_id' => $company_id,
        'indicator_number' => $indicator_number
    ];

    $insertResult = $dbHandler->insertData($table_set, $arrData);

    $message = $insertResult['message'];
    $insert_id = $insertResult['insert_id'];

    $ticketsModel->checkStatusTable($insert_id, $table_set);

    $currentDate = new DateTime();
    $currentMonth = $currentDate->format('m');

    $ticketNumber = $_SESSION['company_id'] . $currentMonth . ((round($insert_id * 2.7)) % 10000) . random_int(10, 99);

    $dbHandler->updateData(
        $table_set,
        ['ticket_number' => $ticketNumber],
        'id = ' . (int) $insert_id
    );

    $emailMembers = [];

    if (is_array($members) && !empty($members)) {
        foreach ($members as $member) {

            $commonData = [
                'receiver_person_id' => $member,
                'receiver_type' => 'a',
                'sender_person_id' => $adminId,
                'sender_rbac_id' => $rbacId,
                'sender_type' => 'a',
                'section_element_id' => $insert_id,
                'section_part_name' => $part_name,
            ];

            $dbHandler->insertData('forwards', $commonData);

            $emailMembers[] = $member;
        }
    }

    if (isset($_FILES['attach_file']) && $_FILES['attach_file']['size'] > 0) {

        $uploadDir = '../irepository/tickets/';
        $uploadDirDb = './irepository/tickets/';
        $fileManager = new FileManager($db, $uploadDir);
        $uploadedFile = $fileManager->uploadFile($_FILES['attach_file']);

        $dbHandler->insertData('file_manage', [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirDb,
            'file_title' => $title,
            'admin_id' => $adminId,
            'part_id' => $insert_id,
            'part_name' => $part_name
        ]);
    }

    header("Location: ./tickets");
    /*
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        ignore_user_abort(true);

        if (!empty($emailMembers)) {
            foreach ($emailMembers as $member) {
                safeSendTicketEmail($config, $adminModel, $member, $ticketNumber, $title);
            }
        }
            */

        exit;
    }
    //////////////////////////////////////////////// تست 



