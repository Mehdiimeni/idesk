<?php

use ipanel\model\AdminModel;
// controller/bi/bi_admins.php

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();


$rbacClass = new RBAC($db);

if ($rbacClass->checkPermissionOperationByName('statistics_operation')) {
    $permissionStatistics = true;
    $adminModel = new AdminModel($db);
    $topPerformingResult = $adminModel->getTopPerforming();
    $lastLoginAdminsResult = $adminModel->getLastLoginAdmins(20);
    $countDailyReportAdminsResult = $adminModel->getCountDailyReportAdmins(20);
} else {
    $permissionStatistics = false;
}


?>