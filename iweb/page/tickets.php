<?php

use iweb\model\TicketModel;
use iweb\model\UserModel;
//page / inbox / tickets

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
if (!$userModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('tickets', 'inbox', 'u')) {
    exit;
}

$ticketModel = new TicketModel($db);

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageUserTitle'] = [];

function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'menu');
    $objFileCaller->includeFileWithController('./iweb', 'ticket/', $specificFile);
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_js');
}

if (isset($_GET['ticket_id']) && !empty($_GET['ticket_id'])) {
    $_SESSION['arrayComponents'] = array('lang_user', 'select2', 'multi', 'flat', 'todo', 'status_user');

    
    $encryptorClass = new Encryptor($config->getConfig('encryptWebKey'));
    $ticket_id_encrypt = $_GET['ticket_id'];
    $ticket_id = (int) $encryptorClass->decrypt($ticket_id_encrypt);
 

    $_SESSION['pageUserTitle'][] = 'ticket';
    $_SESSION['pageUserTitle'][1] = $ticketModel->getTicketNumberById($ticket_id);

    includeFiles($objFileCaller, 'ticket_details');
} elseif (isset($_GET['add']) && !empty($_GET['add'])) {
    $_SESSION['arrayComponents'] = array('lang_user', 'select2', 'multi', 'flat');
    $_SESSION['pageUserTitle'][] = 'new_ticket';
    includeFiles($objFileCaller, 'ticket_add');
} else {
    $_SESSION['arrayComponents'] = array('table',  'lang_user', 'cookie_list_user');
    $_SESSION['pageUserTitle'][] = 'tickets';
    includeFiles($objFileCaller, 'tickets');
}
