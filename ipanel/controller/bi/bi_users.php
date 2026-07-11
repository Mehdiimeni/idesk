<?php

use iweb\model\UserModel;
///controller/bi/bi_users.php

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);
if ($rbacClass->checkPermissionOperationByName('statistics_operation')) {
    $userModel = new UserModel($db);
    $permissionStatistics = true;
    $topPerformingResult = $userModel->getTopPerforming();
    $lastLoginUsersResult = $userModel->getLastLoginUsers();
} else {
    $permissionStatistics = false;
}

