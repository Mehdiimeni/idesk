<?php

use ipanel\model\AdminModel;
//page / inbox / daily_report

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);

if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('daily_report', 'inbox')) {
    exit;
}


$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];

function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('.', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('.', 'global/', 'menu');
    $objFileCaller->includeFileWithController('.', 'admin/', $specificFile);
    $objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_js');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $_SESSION['arrayComponents'] = array('table', 'lang', 'select2', 'date', 'status', 'multi', 'flat');

    $_SESSION['pageTitle'][] = 'daily_report';
    includeFiles($objFileCaller, 'daily_report_details');
} elseif (isset($_GET['add']) && !empty($_GET['add'])) {
    $_SESSION['arrayComponents'] = array('table', 'lang', 'select2', 'date', 'status', 'multi', 'flat');

    $_SESSION['pageTitle'][] = 'daily_report';
    includeFiles($objFileCaller, 'daily_report_add');
} else {
    $_SESSION['arrayComponents'] = array('table', 'main', 'lang');

    $_SESSION['pageTitle'][] = 'daily_report';
    includeFiles($objFileCaller, 'daily_report');
}


