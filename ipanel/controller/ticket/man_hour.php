<?php
///ipanel/controller/ticket/man_hour.php
use ipanel\model\ManHourModel;
use ipanel\model\TicketModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$ticketModel = new TicketModel($db);
$rbacClass = new RBAC($db);
$textToolsClass = TextTools::getInstance();
$manhourModel = new ManHourModel($db);

function getTicketInfo($ticketModel, $manhourModel, $ticket_number)
{
    if ($ticket_number != '') {
        $ticket_id = $ticketModel->getTicketIdByNumber($ticket_number);
        return $manhourModel->getManHourInfo('tickets', $ticket_id);
    }
    return null;
}

if (isset($_GET['ticket_number']) && $_GET['ticket_number'] != '')
    $resultManHour = getTicketInfo($ticketModel, $manhourModel, $_GET['ticket_number']);
