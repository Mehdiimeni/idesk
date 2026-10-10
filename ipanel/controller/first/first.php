<?php
///ipanel/controller/first/first.php
use ipanel\model\ManagerialModel;
use ipanel\model\ProjectManagerModel;
use ipanel\model\StructureModel;
use ipanel\model\TicketModel;


// general class
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));

// Get session variables
$adminId = $_SESSION['admin_id'];
$companyId = $_SESSION['company_id'];
$rbacClassId = $_SESSION['rbac_id'];
$profileImagePath = '.' . $_SESSION['profile_image_path'] . $_SESSION['profile_image_name'];
$profileName = $_SESSION['name'];

$rbacClass = new RBAC($db);
$ticketModel = new TicketModel($db);
$structureModel = new StructureModel($db);
$adminManagerialModel = new ManagerialModel($db);

$encryptorClassClass = new Encryptor($config->getConfig('encryptPanelKey'));
$textToolsClass = TextTools::getInstance();

// model
$projectsModel = new ProjectManagerModel($db);

// is entry
$isEntry = $rbacClass->checkPermissionOperationByName('pointer_operation') ? 1 : 0;

$array_except = array('condition_regect');

$allRequests = $ticketModel->getRequestsByPersonId('a', $array_except);
$allResponse = $ticketModel->getResponseByPersonId('a', $array_except);



$allKanbanTag = $ticketModel->getAllKabanTag();



// projects by percentage
if ($rbacClass->checkPermissionOperationByName('view_project_operation')) {
    $permissionProjects = true;
    $allProjects = $projectsModel->getAllProjects();
} else {
    $permissionProjects = false;
}


// todo by percentage
$allTodo = $adminManagerialModel->getTodoByPercentage(
    $adminId,
    100
);


// profile
$adminCompanyName = $structureModel->getCompanyById($companyId)['company_name'];
$adminRbacName = $structureModel->getRBACById($rbacClassId)['rbac_name'];


// Dashboard current inbox: use the same methods as tickets?referred=0
// so the first-page count stays aligned with the real ticket list and active filters.
$dashboardExceptConditions = [
    'condition_archive',
    'condition_final_done',
    'condition_pendency'
];

if ($isEntry) {
    $dashboardInboxTickets = $ticketModel->getAllAdminNoActionTicketsWithExcept(
        $adminId,
        $dashboardExceptConditions
    );

    $intNoActionTicketCount = (int) $ticketModel->getAllAdminNoActionTicketsWithExceptCount(
        $adminId,
        $dashboardExceptConditions
    );
} else {
    $dashboardInboxTickets = $ticketModel->getAllAdminNoActionTickets($adminId);
    $intNoActionTicketCount = (int) $ticketModel->getAllAdminNoActionTicketsCount($adminId);
}

// Same source as tickets?referred=1.
$intForwardTicketCount = (int) $ticketModel->getAllAdminForwardTicketsCount($adminId);

// Breakdown of the tickets that are actually visible in the current inbox.
$dashboardInboxBreakdown = [
    'high' => 0,
    'medium' => 0,
    'low' => 0,
    'overdue' => 0,
];

$dashboardOverdueThreshold = strtotime('-14 days');

foreach ($dashboardInboxTickets as $dashboardTicket) {
    $dashboardPriority = strtolower((string) ($dashboardTicket['ticket_priority'] ?? ''));

    if (isset($dashboardInboxBreakdown[$dashboardPriority])) {
        $dashboardInboxBreakdown[$dashboardPriority]++;
    }

    if (!empty($dashboardTicket['ticket_creation_date'])) {
        $dashboardCreatedTime = strtotime($dashboardTicket['ticket_creation_date']);

        if (
            $dashboardCreatedTime !== false &&
            $dashboardCreatedTime < $dashboardOverdueThreshold
        ) {
            $dashboardInboxBreakdown['overdue']++;
        }
    }
}

// Management report: number of received/sent ticket forward operations
// during the last 7 and 30 days. No Done/Status/completion-rate KPI.
$dashboardActivityStats = $ticketModel->getAdminDashboardActivityStats($adminId);

// check permission opration person hour and delivery time
if ($rbacClass->checkPermissionOperationByName(operation_name: 'person_hour_operation') || $rbacClass->checkPermissionOperationByName('delivery_time_operation')) {
    $personHourDeliveryTimePermissionOperation = true;
} else {
    $personHourDeliveryTimePermissionOperation = false;
}

// check permission request person hour and delivery time
if ($rbacClass->checkPermissionOperationByName('person_hour_request') || $rbacClass->checkPermissionOperationByName('delivery_time_request')) {
    $personHourDeliveryTimePermissionRequest = true;
} else {
    $personHourDeliveryTimePermissionRequest = false;
}


function getPriorityBadge($priority)
{
    if ($priority == 'low') {
        return '<span class="badge bg-primary float-end">' . _lang['low'] . '</span>';
    } elseif ($priority == 'medium') {
        return '<span class="badge bg-warning float-end">' . _lang['medium'] . '</span>';
    } elseif ($priority == 'high') {
        return '<span class="badge bg-danger float-end">' . _lang['high'] . '</span>';
    }
    return '';
}

// text limit
function getTextLimit($text)
{
    return TextTools::getInstance()->truncateText($text, 85);
}
/*  
$listAutoChangeCondition = [
    [
        'condition_done',
        'condition_acepted_test_auto',
        10
    ],
    [
        'condition_invoice',
        'condition_acepted_invoice_auto',
        12
    ],
];

$ticketModel->SetAutoCondition(
    $listAutoChangeCondition
);
*/


?>