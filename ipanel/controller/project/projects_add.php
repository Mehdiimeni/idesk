<?php
///controller/project/projects_add.php

use ipanel\model\AdminModel;
use ipanel\model\StructureModel;
use ICore\SmtpMailer;



$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$dbHandler = new DatabaseHandler($db);
$structureModel = new StructureModel($db);
$rbacClass = new RBAC($db);
$adminModel = new AdminModel($db);

function safeSendProjectEmail($config, $adminModel, $member, $projectName, $description)
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

        if (method_exists($mailer, 'setTimeout')) {
            $mailer->setTimeout(5);
        }

        $to = $adminEmail['email'];
        $subject = _lang['project'] . ' ' . $projectName;

        $body = "
        <div style='font-family:Tahoma,Arial,sans-serif;font-size:14px;line-height:1.8;color:#333'>
            <h3 style='color:#0d6efd;margin-bottom:15px'>
                " . _lang['dear_admin'] . "
            </h3>

            <p>
                " . _lang['forward_project_email_body1'] . "
            </p>

            <div style='background:#f8f9fa;border:1px solid #dee2e6;padding:12px;border-radius:6px;margin:15px 0'>
                <strong>" . htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8') . "</strong>
                <br><br>
                <span style='white-space:pre-wrap'>" . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . "</span>
            </div>

            <p>جهت بررسی و پیگیری پروژه، لطفاً وارد پنل شوید:</p>

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
        error_log('Project email send failed: ' . $e->getMessage());
        return false;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['name']) || trim($_POST['name']) === '') {
        header("Location: ./projects");
        exit;
    }

    $name = trim($_POST['name']);
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $members = $_POST['members'] ?? null;

    $adminId = $_SESSION['admin_id'];
    $part_name = 'projects';
    $table_set = 'projects';

    $arrData = [
        'name' => $name,
        'description' => $description,
        'start_date' => $start_date,
        'admin_id' => $adminId
    ];

    if ($end_date !== null) {
        $arrData['end_date'] = $end_date;
    }

    $insertResult = $dbHandler->insertData($table_set, $arrData);

    $message = $insertResult['message'];
    $insert_id = $insertResult['insert_id'];

    $emailMembers = [];

    // members
    if (is_array($members) && !empty($members)) {
        foreach ($members as $member) {

            $commonData = [
                'member_id' => $member,
                'member_type' => 'a',
                'admin_id' => $adminId,
                'admin_type' => 'a',
                'element_id' => $insert_id,
                'part_name' => $part_name,
            ];

            $dbHandler->insertData('project_members', $commonData);

            $emailMembers[] = $member;
        }
    }

    // add file
    if (!empty($_FILES['attach_file']['size'])) {

        $uploadDir = '../irepository/projects/';
        $uploadDirDb = './irepository/projects/';

        $fileManager = new FileManager($db, $uploadDir);
        $uploadedFile = $fileManager->uploadFile($_FILES['attach_file']);

        $fileData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirDb,
            'file_title' => $name,
            'admin_id' => $adminId,
            'part_id' => $insert_id,
            'part_name' => $part_name
        ];

        $dbHandler->insertData('file_manage', $fileData);
    }

    header("Location: ./projects");
    /*
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        ignore_user_abort(true);

        if (!empty($emailMembers)) {
            foreach ($emailMembers as $member) {
                safeSendProjectEmail($config, $adminModel, $member, $name, $description);
            }
        }
            */

        exit;
    }
