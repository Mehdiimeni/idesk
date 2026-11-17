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
if (!$rbacClass->checkPermissionSubpartByName('users', 'members', 'systems')) {
    http_response_code(403);
    exit;
}

$_SESSION['arrayComponents'] = array('main', 'lang', 'select2');
$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = ['customer_portal'];

if (!function_exists('includeCustomerPortalAdminFiles')) {
    function includeCustomerPortalAdminFiles($objFileCaller, $specificFile)
    {
        $objFileCaller->includeFileWithController('.', 'global/', 'page_icon');
        $objFileCaller->includeFileWithController('.', 'global/', 'page_css');
        $objFileCaller->includeFileWithController('.', 'global/', 'page_top');
        $objFileCaller->includeFileWithController('.', 'global/', 'menu');
        $objFileCaller->includeFileWithController('.', 'member/', $specificFile);
        $objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
        $objFileCaller->includeFileWithController('.', 'global/', 'page_js');
    }
}
includeCustomerPortalAdminFiles($objFileCaller, 'customer_portal_admin');
