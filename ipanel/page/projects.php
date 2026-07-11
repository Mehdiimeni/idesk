<?php

use ipanel\model\AdminModel;
//page / project / projects 

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);

if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('projects', 'project')) {
    exit;
}

$_SESSION['arrayComponents'] = array('lang', 'todo', 'select2', 'date', 'multi', 'status', 'flat');
$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];
function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('.', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('.', 'global/', 'menu');
    $objFileCaller->includeFileWithController('.', 'project/', $specificFile);
    $objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_js');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $_SESSION['pageTitle'][] = 'projects';
    includeFiles($objFileCaller, 'projects_details');
} elseif (isset($_GET['add']) && !empty($_GET['add'])) {
    $_SESSION['pageTitle'][] = 'projects';
    includeFiles($objFileCaller, 'projects_add');
} else {
    $_SESSION['pageTitle'][] = 'projects';
    includeFiles($objFileCaller, 'projects');
}

