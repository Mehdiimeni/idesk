<?php

use iweb\model\UserModel;
//page / ticket / priority_list

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
if (!$userModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('priority_list', 'inbox', 'u')) {
    exit;
}

$_SESSION['arrayComponents'] = array('lang_user', 'select2', 'multi', 'flat');
$objFileCaller = FileCaller::getInstance();
$_SESSION['pageUserTitle'] = [];

function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'menu');
    $objFileCaller->includeFileWithController('./iweb', 'ticket/', $specificFile);
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_js');
}
$_SESSION['pageUserTitle'][] = 'priority_list';
includeFiles($objFileCaller, 'priority_list');
