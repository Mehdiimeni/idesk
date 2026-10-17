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
    if (!empty($ticket_id)) {
        return $ticketModel->getStatusAndForwards($ticket_id, 'tickets');
    }

    return [];
}

$workflowTicketNumber = isset($_GET['ticket_number'])
    ? trim((string) $_GET['ticket_number'])
    : '';

$ticket_id = null;
$resultTicket = [];
$ticketHeader = null;
$timeDifference = [
    'status' => 'success',
    'hours' => 0,
];

if ($workflowTicketNumber !== '') {
    $ticket_id = $ticketModel->getTicketIdByNumber($workflowTicketNumber);

    if (!empty($ticket_id)) {
        $resultTicket = getTicketInfo($ticketModel, $ticket_id);
        $timeDifference = $ticketModel->getTimeDifference($ticket_id, 'tickets');

        /*
         * Workflow header information.
         * ticket_user_view already resolves the original ticket sender from users/admins
         * and exposes the sender company/unit without changing any legacy table.
         */
        $sql = "
            SELECT
                ticket_id,
                ticket_number,
                ticket_title,
                ticket_status,
                priority,
                type_name,
                type_group,
                name AS sender_name,
                company_name,
                unit_name,
                creation_date
            FROM ticket_user_view
            WHERE ticket_id = ?
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);

        if ($stmt !== false) {
            $stmt->bind_param('i', $ticket_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $ticketHeader = $result ? $result->fetch_assoc() : null;
            $stmt->close();
        }
    }
}
