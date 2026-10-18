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

/*
 * user_view v4 owns its save flow and no longer uses legacy operation.js.
 * Keeping only lang avoids the old unprotected add_operation.php mutation path.
 */
$_SESSION['arrayComponents'] = array('lang');

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = ['user_view'];

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

if ((isset($_GET['rbac_id']) && (int)$_GET['rbac_id'] > 0) ||
    (isset($_GET['id']) && (int)$_GET['id'] > 0)) {
    includeFiles($objFileCaller, 'user_view_details');
} else {
    includeFiles($objFileCaller, 'user_view');
}
