<?php
///controller/ticket/priority_list_logs.php
use ipanel\model\AdminPriorityService;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$service = new AdminPriorityService($db);

// auth
if (!isset($_SESSION['admin_id'])) {
    throw new \RuntimeException("Unauthorized");
}

$flash = ['ok' => null, 'err' => null];

try {

    // Get logs
    $listId = (int) ($_GET['list_id'] ?? 0);
    if ($listId <= 0)
        throw new \RuntimeException("list_id required");

    $data = $service->getLogsAndComments($listId);
    $list = $data['list'];
    $companyName = $data['companyName'];
    $listComments = $data['listComments'];
    $listLogs = $data['listLogs'];
    $itemLogs = $data['itemLogs'];
    $itemComments = $data['itemComments'];

} catch (Throwable $e) {
    $flash['err'] = $e->getMessage();
    $list = null;
    $companyName = null;
    $listComments = [];
    $listLogs = [];
    $itemLogs = [];
    $itemComments = [];
}
