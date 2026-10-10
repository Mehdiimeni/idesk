<?php
///iweb/controller/first/first.php
use iweb\model\StructureModel;
use iweb\model\TicketModel;
use iweb\model\UserModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);

if (!$userModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}
$rbacClass = new RBAC($db);
$ticketModel = new TicketModel($db);
$structureModel = new StructureModel($db);
$allConditions = $structureModel->getConditionsByPart('tickets');

$textToolsClass = TextTools::getInstance();

$encryptorClass = new Encryptor($config->getConfig('encryptWebKey'));

// Dashboard scope: company-wide only for users who already have permission to view all company tickets.
$dashboardCompanyId = $rbacClass->checkPermissionOperationByName('view_all_ticket_operation', 'u')
    ? (int) $_SESSION['company_id']
    : null;

$dashboardStats = $ticketModel->getDashboardStats($dashboardCompanyId);
$dashboardStatusSummary = $ticketModel->getDashboardStatusSummary($dashboardCompanyId);
$dashboardFinanceStatusSummary = $ticketModel->getDashboardFinanceStatusSummary($dashboardCompanyId);
$dashboardRecentTickets = $ticketModel->getDashboardRecentTickets($dashboardCompanyId, 6);

// Build one status metadata map so the template does not query Conditions row-by-row.
$dashboardConditionMeta = [];
if ($allConditions) {
    while ($conditionRow = $allConditions->fetch_assoc()) {
        $dashboardConditionMeta[strtolower($conditionRow['condition_name'])] = $conditionRow;
    }
}

// Dashboard action queues are intentionally limited to the last 14 days.
// Older confirmation items are handled by the automatic scheduled process.
$dashboardActionWindowDays = 14;

if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')) {
    $condition_name = 'condition_done';
    $testTickets = $ticketModel->getDashboardRecentStatusTickets($condition_name, $dashboardActionWindowDays);
}

if ($rbacClass->checkPermissionOperationByName('condition_acepted_invoice', 'u')) {
    $condition_name = 'condition_invoice';
    $invoiceTickets = $ticketModel->getDashboardRecentStatusTickets($condition_name, $dashboardActionWindowDays);
}

if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')) {
    $condition_name = 'condition_regect';
    $commentTicketsReject = $ticketModel->getTicketRejectDescription($condition_name, null, $dashboardActionWindowDays);
}

$condition_name = 'condition_need_action';
$commentTicketsNeed = $ticketModel->getTicketRejectDescription($condition_name, null, $dashboardActionWindowDays);

//Kaban
if ($rbacClass->checkPermissionOperationByName('kanban_board_operation','u')) {
    $permissionKabanBoard = true;
    $allKanbanTag = $ticketModel->getAllKabanTag();
} else {
    $permissionKabanBoard = false;
}