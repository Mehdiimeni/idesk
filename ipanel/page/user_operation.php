<?php
use ipanel\model\AdminModel;

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
$_SESSION['pageTitle'] = ['user_operation'];
$_SESSION['arrayComponents'] = array('table', 'lang');

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

// v2 uses one protected editor on the main route. Legacy ?add / ?id URLs
// still land here so menu/RBAC routes remain backward compatible.
includeFiles($objFileCaller, 'user_operation');
