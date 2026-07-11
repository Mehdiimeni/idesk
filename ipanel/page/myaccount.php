<?php

use ipanel\model\AdminModel;
//page / myaccount

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);

if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$_SESSION['arrayComponents'] = array('lang');

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
<<<<<<< HEAD
$_SESSION['pageTitle'][] = 'my_account';
=======

>>>>>>> 5591029... some change
includeFiles($objFileCaller, 'myaccount');


