<?php

use ipanel\model\AdminModel;
//page / access / user_operation

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();


$admin = new AdminModel($db);

if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('user_operation', 'access')) {
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
    $objFileCaller->includeFileWithController('.', 'structure/', $specificFile);
    $objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_js');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $_SESSION['arrayComponents'] = array('tree', 'main', 'lang');
    $_SESSION['pageTitle'][] = 'user_operation';
    includeFiles($objFileCaller, 'user_operation_details');
} elseif (isset($_GET['add']) && !empty($_GET['add'])) {
    $_SESSION['arrayComponents'] = array('tree', 'lang');
    $_SESSION['pageTitle'][] = 'user_operation';
    includeFiles($objFileCaller, 'user_operation_add');
} else {
    $_SESSION['arrayComponents'] = array('table', 'lang');
    $_SESSION['pageTitle'][] = 'user_operation';
    includeFiles($objFileCaller, 'user_operation');
}

