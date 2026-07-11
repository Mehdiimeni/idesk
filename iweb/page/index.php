<?php

use iweb\model\TicketModel;
use iweb\model\UserModel;
//page / index

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
$ticketModel = new TicketModel($db);


if (!$userModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}


$_SESSION['arrayComponents'] = array('lang_user');

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageUserTitle'] = [];
function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'menu');
    $objFileCaller->includeFileWithController('./iweb', 'first/', $specificFile);
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_js');
}
$_SESSION['pageUserTitle'][] = 'my_dashboard';
includeFiles($objFileCaller, 'first');

