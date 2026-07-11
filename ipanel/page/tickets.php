<?php

use ipanel\model\AdminModel;
use ipanel\model\TicketModel;
//page / inbox / tickets

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$ticketModel = new TicketModel($db);

if (!$admin->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$rbacClass = new RBAC($db);
if (!$rbacClass->checkPermissionPartByName('tickets', 'inbox')) {
    exit;
}

$_SESSION['pageTitle'] = [];
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

if (isset($_GET['ticket_id']) && !empty($_GET['ticket_id'])) {
    $_SESSION['arrayComponents'] = array('lang', 'select2', 'multi', 'flat', 'todo', 'status');

    $encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));

    $ticket_id_encrypt = $_GET['ticket_id'];
    $ticket_id = (int) $encryptorClass->decrypt($ticket_id_encrypt);

    $_SESSION['pageTitle'][] = 'ticket';
    $_SESSION['pageTitle'][1] = $ticketModel->getTicketNumberById($ticket_id);
    includeFiles($objFileCaller, 'ticket_details');
} elseif (isset($_GET['add']) && !empty($_GET['add'])) {
    $_SESSION['arrayComponents'] = array('lang', 'select2', 'multi', 'flat');
    // page title
    $_SESSION['pageTitle'][] = 'new_ticket';
    includeFiles($objFileCaller, 'ticket_add');
} else {
    $_SESSION['arrayComponents'] = array('table', 'lang', 'cookie_list');
    // page title
    $_SESSION['pageTitle'][] = 'tickets';
    includeFiles($objFileCaller, 'tickets');
}


