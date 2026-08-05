<?php

use ipanel\model\AdminModel;
//page / ticket / department_current_works

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$adminModel = new AdminModel($db);

if (!$adminModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$_SESSION['arrayComponents'] = array('table', 'lang');

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];

function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('.', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('.', 'global/', 'menu');
    $objFileCaller->includeFileWithController('.', 'ticket/', $specificFile);
    $objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('.', 'global/', 'page_js');
}

$_SESSION['pageTitle'][] = 'department_current_works';
includeFiles($objFileCaller, 'department_current_works');
