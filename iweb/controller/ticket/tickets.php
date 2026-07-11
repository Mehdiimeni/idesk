<?php
///iweb/controller/ticket/tickets.php
use iweb\model\StructureModel;
use iweb\model\TicketModel;
use iweb\model\UserModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
$ticketModel = new TicketModel($db);
$rbacClass = new RBAC($db);


$part_name = 'tickets';

$structureModel = new StructureModel($db);

$textToolsClass = TextTools::getInstance();

$encryptorClass = new Encryptor($config->getConfig('encryptWebKey'));




$allConditions = $structureModel->getConditionsByPart($part_name);


// priority
function getPriorityBadge($priority)
{
    if ($priority == 'low') {
        return '<span class="badge bg-primary ">' . _lang['low'] . '</span>';
    } elseif ($priority == 'medium') {
        return '<span class="badge bg-warning ">' . _lang['medium'] . '</span>';
    } elseif ($priority == 'high') {
        return '<span class="badge bg-danger ">' . _lang['high'] . '</span>';
    }
    return '';
}

//
$typesResult = $structureModel->getTypes();



// view permission
if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')) {
    $permissionUserView = true;
} else {
    $permissionUserView = false;
}


/// button
$baseQueryParams = [];
if (isset($_GET['referred'])) {
    $baseQueryParams['referred'] = $_GET['referred'];
}

$showAddTicketButton = $permissionAddTicket ?? false;
$addTicketUrl = './tickets?add=r';
if (!empty($baseQueryParams)) {
    $addTicketUrl .= '&' . http_build_query($baseQueryParams);
}



$viewParamsNormal = $baseQueryParams;
$viewParamsWithDetails = array_merge($baseQueryParams, ['details' => 'long']);

$viewUrlNormal = './tickets' . (!empty($viewParamsNormal) ? '?' . http_build_query($viewParamsNormal) : '');
$viewUrlWithDetails = './tickets?' . http_build_query($viewParamsWithDetails);



// marks list
$userId = $_SESSION['user_id'];
$companyId = $_SESSION['company_id'];
$markListsResult = $structureModel->getAllMarkingTags($userId, $companyId);

// mark
$ticketMarks = [];

while ($row = $markListsResult->fetch_assoc()) {
    $ticketMarks[] = $row;
}
////

$columns = [
    ["data" => "ticket_id", "title" => "#", "visible" => false],
    ["data" => "ticket_number", "title" => _lang['ticket_number']],
    ["data" => "ticket_creation_date_shamsi", "title" => _lang['added_date']],
    ["data" => "type_group", "title" => _lang['group']],
    ["data" => "priority", "title" => _lang['priority']],
    ["data" => "ticket_comments", "title" => _lang['comments']],
    ["data" => "ticket_title_html", "title" => _lang['title']],
    ["data" => "ticket_status", "title" => _lang['status']],
    ["data" => "user_name_display", "title" => _lang['user']],
    ["data" => "type_name_display", "title" => _lang['type']],
    ["data" => "indicator_number_display", "title" => _lang['indicator']],
];


$columns[] = ["data" => "actions_html", "title" => _lang['action'], "orderable" => false, "searchable" => false];

$datatableColumnsJson = json_encode($columns, JSON_UNESCAPED_UNICODE);
