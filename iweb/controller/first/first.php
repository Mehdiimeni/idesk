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

if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')) {
    $condition_name = 'condition_done';
    $testTickets = $ticketModel->getTicketIfTimeOverSetAutoCondition($condition_name,'condition_acepted_test_auto',10);
}

if ($rbacClass->checkPermissionOperationByName('condition_acepted_invoice', 'u')) {
    $condition_name = 'condition_invoice';
    $invoiceTickets = $ticketModel->getTicketIfTimeOverSetAutoCondition($condition_name,'condition_acepted_invoice_auto',10);
}

if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')) {
    $condition_name = 'condition_regect';
    $commentTicketsReject = $ticketModel->getTicketRejectDescription($condition_name);
}

$condition_name = 'condition_need_action';
$commentTicketsNeed = $ticketModel->getTicketRejectDescription($condition_name);

//Kaban
if ($rbacClass->checkPermissionOperationByName('kanban_board_operation','u')) {
    $permissionKabanBoard = true;
    $allKanbanTag = $ticketModel->getAllKabanTag();
} else {
    $permissionKabanBoard = false;
}