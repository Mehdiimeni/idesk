<?php
///ipanel/controller/admin/daily_report_add.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$dbHandler = new DatabaseHandler($db);
$rbacClass = new RBAC($db);
$textToolsClass = TextTools::getInstance();

//$unitsResult = $structureModel->getUnitsByCompany($_SESSION['company_id']);
$allCompanies = $structureModel->getCompanies();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_name = 'reports';
    $table_set = 'daily_reports';

    $subject = $_POST['subject'];
    $strRequest = $_POST['request'];
    $arrRequest = explode("&&", $strRequest);
    $description = $_POST['description'];
    $progress_percentage = $_POST['progress_percentage'];
    $priority = $_POST['priority'];
    $start_date = $_POST['start_date'];
    $end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : null;


    $arrData = [
        'subject' => $subject,
        'company_id' => $arrRequest[0],
        'member_id' => $arrRequest[1],
        'company_name' => $arrRequest[2],
        'member_name' => $arrRequest[3],
        'description' => $description,
        'progress_percentage' => $progress_percentage,
        'priority' => $priority,
        'start_date' => $start_date,
        'admin_id' => $_SESSION['admin_id']
    ];

    if ($end_date != null) {
        $arrData['end_date'] = $end_date;
    }


    
    $insertResult = $dbHandler->insertData($table_set, $arrData);
    $insert_id = $insertResult['insert_id'];

    if (!empty($_FILES['attach_file']['size'])) {
        $uploadDir = '../irepository/reports/';
        $uploadDirDb = './irepository/reports/';
        $fileManager = new FileManager($db, $uploadDir);
        $uploadedFile = $fileManager->uploadFile($_FILES['attach_file']);

        $fileData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirDb,
            'file_title' => $subject,
            'admin_id' => $_SESSION['admin_id'],
            'part_id' => $insert_id,
            'part_name' => $part_name
        ];

        $dbHandler->insertData('file_manage', $fileData);
    }

    $dbHandler->insertData('daily_report_progress_logs', [
        'report_id' => $insert_id,
        'admin_id' => $_SESSION['admin_id'],
        'progress_percentage' => $progress_percentage,
        'log_type' => 'create'
    ]);

    header("Location: ./daily_report");
    exit;
}
