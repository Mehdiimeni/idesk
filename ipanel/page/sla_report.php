<?php

use ipanel\model\AdminModel;
//page / dashboard / sla_report

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);


if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('sla_report', 'dashboard')) {
    exit;
}

$_SESSION['arrayComponents'] = array('table', 'flat', 'date', 'lang');

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

$_SESSION['pageTitle'][] = 'sla_report';
includeFiles($objFileCaller, 'sla_report');

