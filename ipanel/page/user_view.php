<?php

use ipanel\model\AdminModel;
// page / access / user_view

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);

if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);

if (!$rbacClass->checkPermissionPartByName('user_view', 'access')) {
    exit();
}

$_SESSION['arrayComponents'] = array('operation', 'table', 'lang');

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
    $_SESSION['pageTitle'][] = 'user_view';
    includeFiles($objFileCaller, 'user_view_details');
} else {
    $_SESSION['pageTitle'][] = 'user_view';
    includeFiles($objFileCaller, 'user_view');
}
