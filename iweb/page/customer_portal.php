<?php

use iweb\model\UserModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
if (!$userModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
$objFileCaller = FileCaller::getInstance();
$_SESSION['arrayComponents'] = array('lang_user', 'select2', 'multi', 'flat');
$_SESSION['pageUserTitle'] = ['customer_portal'];

if (!function_exists('includeCustomerPortalFiles')) {
    function includeCustomerPortalFiles($objFileCaller, $specificFile)
    {
        $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_icon');
        $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_css');
        $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_top');
        $objFileCaller->includeFileWithController('./iweb', 'global/', 'menu');
        $objFileCaller->includeFileWithController('./iweb', 'customer_portal/', $specificFile);
        $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_footer');
        $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_js');
    }
}

includeCustomerPortalFiles($objFileCaller, 'customer_portal');
