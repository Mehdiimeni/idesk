<?php
///controller/ticket/priority_list.php
use ipanel\model\AdminPriorityService;
use ipanel\model\StructureModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$service = new AdminPriorityService($db);
$structureModel = new StructureModel($db);

$part_name = 'priority_list_admin';

// auth
if (!isset($_SESSION['admin_id'])) {
    throw new \RuntimeException("Unauthorized");
}
$adminId = (int) $_SESSION['admin_id'];

$action = isset($_GET['action']) ? trim($_GET['action']) : 'list';

$flash = ['ok' => null, 'err' => null];

try {

    // list page
    $filterCompanyId = isset($_GET['company_id']) && $_GET['company_id'] !== '' ? (int) $_GET['company_id'] : null;
    $filterTypeGroup = isset($_GET['type_group']) && $_GET['type_group'] !== '' ? trim($_GET['type_group']) : null;
    $filterStatus = isset($_GET['status']) && $_GET['status'] !== '' ? trim($_GET['status']) : null;

    $lists = $service->getLists($filterCompanyId, $filterTypeGroup, $filterStatus, 300);
    $typeGroups = $service->getTypeGroups();
    $companies = $service->getCompanies();


} catch (Throwable $e) {
    $flash['err'] = $e->getMessage();
    $lists = [];
    $typeGroups = [];
    $companies = [];
}
