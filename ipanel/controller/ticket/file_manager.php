<?php
///ipanel/controller/ticket/file_manager.php
use ipanel\model\AdminModel;
use ipanel\model\TicketModel;
use iweb\model\UserModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$userModel = new UserModel($db);
$ticketModel = new TicketModel($db);
$rbacClass = new RBAC($db);
$textToolsClass = TextTools::getInstance();

$uploadDir = '../irepository/tickets/';
$fileManager = new FileManager($db, $uploadDir);
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));


function getTicketInfo($ticketModel, $ticket_number)
{
    if ($ticket_number != '') {
        $ticket_id = $ticketModel->getTicketIdByNumber($ticket_number);
        return $ticketModel->getFileingInfo('tickets', $ticket_id);
    }
    return null;
}

if (isset($_GET['ticket_number']) && $_GET['ticket_number'] != '')
    $resultFileing = getTicketInfo($ticketModel, $_GET['ticket_number']);

