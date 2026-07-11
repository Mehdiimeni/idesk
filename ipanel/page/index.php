<?php

use ipanel\model\AdminModel;
//page / index

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$adminModel = new AdminModel($db);

if (!$adminModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$_SESSION['arrayComponents'] = array('table', 'lang', 'mask', 'select2', 'confirm');
$_SESSION['pageTitle'] = [];

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];
function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('.', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('.', 'global/', 'menu');
    $objFileCaller->includeFileWithController('.', 'first/', $specificFile);
    $objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_js');
}

// page title
$_SESSION['pageTitle'][] = 'my_dashboard';
includeFiles($objFileCaller, 'first');

