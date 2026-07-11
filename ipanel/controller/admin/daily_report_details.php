<?php
///ipanel/controller/admin/daily_report_details.php
use ipanel\model\AdminModel;
use ipanel\model\ManagerialModel;
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$rbacClass = new RBAC($db);
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$adminManagerialModel = new ManagerialModel($db);


if (isset($_GET['id']))
    $report_id_encrypt = $_GET['id'];


$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));
$report_id = (int) $encryptorClass->decrypt($report_id_encrypt);

$part_name = 'reports';
$table_set = 'daily_reports';
$uploadDir = '../irepository/reports/';
$uploadDirDb = './irepository/reports/';

// get data
$report_detials = $adminManagerialModel->getDailyReport($report_id);

$fileManager = new FileManager($db, $uploadDir);
$dbHandler = new DatabaseHandler($db);
$allFileData = $fileManager->getFileManageByPart($report_id, $part_name);

$allFileInfo = [];

if ($allFileData && $allFileData->num_rows > 0) {
    while ($fileData = $allFileData->fetch_assoc()) {
        $allFileInfo[] = $fileManager->getFileInfoFromPath(
            "." . $fileData['file_path'] . $fileData['file_name'],
            $fileData['file_path'],
            $fileData['file_title']
        );
    }
}

if (isset($_GET['file']) && !empty($_GET['file'])) {
    $fileManager->fileDownload($_GET['file']);
}

// log actions
$progressLogs = $adminManagerialModel->getDailyReportProgressLogs($report_id);
$report_detials = $adminManagerialModel->getDailyReport($report_id);

$isManagerOfReportOwner = $adminManagerialModel->isManagerOfPersonRecursive(
    $_SESSION['admin_id'],
    $report_detials['admin_id']
);

$approvalLog = $adminManagerialModel->getDailyReportApprovalLog($report_id);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    if (isset($_POST['action_type']) && $_POST['action_type'] === 'approve_report') {

        $reportData = $adminManagerialModel->getDailyReport($id);

        $isManagerOfReportOwner = $adminManagerialModel->isManagerOfPersonRecursive(
            $_SESSION['admin_id'],
            $reportData['admin_id']
        );

        if (
            $isManagerOfReportOwner &&
            (int) $_SESSION['admin_id'] !== (int) $reportData['admin_id']
        ) {
            $adminManagerialModel->approveDailyReport($id, $_SESSION['admin_id']);
        }

        header("Location: ./daily_report");
        exit;
    }

    $part_name = 'reports';
    $table_set = 'daily_reports';

    $description = $_POST['description'] ?? '';
    $progress_percentage = $_POST['progress_percentage'] ?? 0;
    $priority = $_POST['priority'] ?? 'low';
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $_POST['last_end_date'];

    $arrData = [
        'description' => $description,
        'progress_percentage' => $progress_percentage,
        'priority' => $priority,
        'end_date' => $end_date,
    ];

    $whereCondition = 'id = ' . $id;

    $updateResult = $dbHandler->updateData($table_set, $arrData, $whereCondition);

    if (isset($_FILES['attach_file']) && $_FILES['attach_file']['size'] > 0) {

        $uploadDir = '../irepository/reports/';
        $uploadDirDb = './irepository/reports/';
        $fileManager = new FileManager($db, $uploadDir);

        $uploadedFile = $fileManager->uploadFile($_FILES['attach_file']);

        if ($uploadedFile) {
            $table_set = 'file_manage';

            $arrData = [
                'file_name' => $uploadedFile,
                'file_path' => $uploadDirDb,
                'file_title' => $_POST['subject'] ?? '',
                'admin_id' => $_SESSION['admin_id'],
                'part_id' => $id,
                'part_name' => $part_name
            ];

            $insertResult = $dbHandler->insertData($table_set, $arrData);
        }
    }

    $dbHandler->insertData('daily_report_progress_logs', [
        'report_id' => $id,
        'admin_id' => $_SESSION['admin_id'],
        'progress_percentage' => $progress_percentage,
        'log_type' => 'edit'
    ]);

    header("Location: ./daily_report");
    exit;
}

