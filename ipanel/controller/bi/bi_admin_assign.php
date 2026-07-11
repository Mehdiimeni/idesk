<?php
///ipanel/controller/bi/bi_admin_assign.php
use ipanel\model\AdminModel;
use ipanel\model\TicketModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);

if ($rbacClass->checkPermissionOperationByName('statistics_operation')) {
    $permissionStatistics = true;
    $adminModel = new AdminModel($db);
    $lastLoginAdminsResult = $adminModel->getLastLoginAdmins( 26);
    $ticketModel = new TicketModel($db);
} else {
    $permissionStatistics = false;
}
