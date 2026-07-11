<?php
///controller/bi/bi_admin_productivity.php
use ipanel\model\AdminModel;
use ipanel\model\KPIModel;
use ipanel\model\TicketModel;

// global class
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);

$startReport = 30;
$endReport = 0;
$loginAdmin = 30;

// model
$kpiModel = new KPIModel($db);
$kpiModel->setTimeReport( $startReport, $endReport);

if ($rbacClass->checkPermissionOperationByName('statistics_operation')) {
    $permissionStatistics = true;
    $adminModel = new AdminModel($db);
    $lastLoginAdminsResult = $adminModel->getLastLoginAdmins( $loginAdmin);
    $ticketModel = new TicketModel($db);
    $ticketModel->setTimeReport($startReport, $endReport);
} else {
    $permissionStatistics = false;
}