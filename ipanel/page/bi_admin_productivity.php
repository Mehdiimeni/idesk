<?php

use ipanel\model\AdminModel;
//page / dashboard / bi_admin_productivity

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);


if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('bi_admin_productivity', 'dashboard')) {
    exit;
}

$_SESSION['arrayComponents'] = array('lang', 'date', 'flat', 'range', 'select2');

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];

function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('.', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('.', 'global/', 'menu');
    $objFileCaller->includeFileWithController('.', 'bi/', $specificFile);
    $objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_js');
}

$_SESSION['pageTitle'][] = 'report_admins_productivity';
includeFiles($objFileCaller, 'bi_admin_productivity');

