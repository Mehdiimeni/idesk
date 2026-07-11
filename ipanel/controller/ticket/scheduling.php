<?php
///controller/ticket/scheduling.php
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

function getTicketInfo($ticketModel, $ticket_number)
{
    if ($ticket_number != '') {
        $ticket_id = $ticketModel->getTicketIdByNumber($ticket_number);
        return $ticketModel->getSchedulingInfo('tickets', $ticket_id);
    }
    return null;
}

if (isset($_GET['ticket_number']) && $_GET['ticket_number'] != '')
    $resultScheduling = getTicketInfo($ticketModel, $_GET['ticket_number']);
