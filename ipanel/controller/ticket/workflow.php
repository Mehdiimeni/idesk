<?php
///ipanel/controller/ticket/workflow.php
use ipanel\model\AdminModel;
use ipanel\model\StructureModel;
use ipanel\model\TicketModel;
use iweb\model\UserModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$userModel = new UserModel($db);
$ticketModel = new TicketModel($db);
$rbacClass = new RBAC($db);
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();

function getTicketInfo($ticketModel, $ticket_id)
{
    if ($ticket_id != '') {
        return $ticketModel->getStatusAndForwards($ticket_id, 'tickets');
    }
    return null;
}

if (isset($_GET['ticket_number']) && $_GET['ticket_number'] != '') {
    $ticket_id = $ticketModel->getTicketIdByNumber($_GET['ticket_number']);
    $resultTicket = getTicketInfo($ticketModel, $ticket_id);
    $timeDifference = $ticketModel->getTimeDifference($ticket_id, 'tickets');
}
