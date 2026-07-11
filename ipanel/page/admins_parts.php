<?php

use ipanel\model\AdminModel;
//page / systems / admins_parts / admins_parts

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);


if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionSubpartByName('admins_parts', 'admins_parts', 'systems')) {
    exit;
}

$_SESSION['arrayComponents'] = array('table', 'main', 'lang');
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
$_SESSION['pageTitle'][] = 'admins_parts';
includeFiles($objFileCaller, 'admins_parts');