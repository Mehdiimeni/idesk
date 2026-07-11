<?php
///ipanel/controller/ticket/tickets.php
use ipanel\model\StructureModel;
use ipanel\model\TicketModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);
$ticketModel = new TicketModel($db);

$part_name = 'tickets';
$structureModel = new StructureModel($db);

// marks list
$adminId = $_SESSION['admin_id'];
$companyId = $_SESSION['company_id'];
$markListsResult = $structureModel->getAllMarkingTags($adminId, $companyId);

// filter view
if ($rbacClass->checkPermissionOperationByName('table_filter_operation')) {

    $permissionTableFilter = true;
    // get company
    $company_profilesResult = $structureModel->getCompanies();
    // get unit
    $unitsResult = $structureModel->getUnits();
    // get type
    $typesResult = $structureModel->getTypes();
    // get condination
    $allConditions = $structureModel->getConditionsByPart($part_name);

} else {

    $permissionTableFilter = false;
}

// view ticket location
if ($rbacClass->checkPermissionOperationByName('view_location_operation') || (isset($_GET['referred']) && $_GET['referred'] == 1)) {
    $permissionViewLocation = true;
} else {
    $permissionViewLocation = false;
}

// view permission
if ($rbacClass->checkPermissionOperationByName('view_operation')) {
    $permissionView = true;
} else {
    $permissionView = false;
}

// view before status permission
if ($rbacClass->checkPermissionOperationByName('view_before_status_operation')) {
    $permissionViewBeforeStatus = true;
} else {
    $permissionViewBeforeStatus = false;
}

// add ticket permission
if ($rbacClass->checkPermissionOperationByName('add_ticket_operation')) {
    $permissionAddTicket = true;
} else {
    $permissionAddTicket = false;
}



// view indicator number permission
if ($rbacClass->checkPermissionOperationByName('view_indicator_number_operation')) {
    $permissionViewIndicatorNumbert = true;
} else {
    $permissionViewIndicatorNumbert = false;
}


// view workflow part permission
if ($rbacClass->checkPermissionPartByName('workflow', 'workflow')) {
    $permissionViewWorkflowPart = true;
} else {
    $permissionViewWorkflowPart = false;
}

// view man hour part permission
if ($rbacClass->checkPermissionPartByName('man_hour', 'workflow')) {
    $permissionViewManHourPart = true;
} else {
    $permissionViewManHourPart = false;
}

// view file manager part permission
if ($rbacClass->checkPermissionPartByName('file_manager', 'workflow')) {
    $permissionViewFileManagerPart = true;
} else {
    $permissionViewFileManagerPart = false;
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

// mark
$ticketMarks = [];

while ($row = $markListsResult->fetch_assoc()) {
    $ticketMarks[] = $row;
}


$viewParamsNormal = $baseQueryParams;
$viewParamsWithDetails = array_merge($baseQueryParams, ['details' => 'long']);

$viewUrlNormal = './tickets' . (!empty($viewParamsNormal) ? '?' . http_build_query($viewParamsNormal) : '');
$viewUrlWithDetails = './tickets?' . http_build_query($viewParamsWithDetails);


////

$columns = [
    ["data" => "ticket_id", "title" => "#", "visible" => false],
    ["data" => "ticket_number", "title" => _lang['ticket_number']],
    ["data" => "ticket_creation_date_shamsi", "title" => _lang['added_date']],
    ["data" => "type_group", "title" => _lang['group']],
    ["data" => "ticket_priority", "title" => _lang['priority']],
    ["data" => "ticket_comments", "title" => _lang['comments']],
    ["data" => "ticket_title_html", "title" => _lang['title']],
    ["data" => "non_finance_status_html", "title" => _lang['status']],
    ["data" => "finance_status_html", "title" => _lang['finance']],
    ["data" => "company_name_html", "title" => _lang['company']],
    ["data" => "user_name_display", "title" => _lang['user']],
    ["data" => "type_name_display", "title" => _lang['type']],
];

if ($permissionViewLocation) {
    $columns[] = ["data" => "last_receiver_name_display", "title" => _lang['inbox']];

}

if ($permissionAddTicket or $permissionViewIndicatorNumbert) {
    $columns[] = ["data" => "indicator_number_display", "title" => _lang['indicator']];
}

$columns[] = ["data" => "actions_html", "title" => _lang['action'], "orderable" => false, "searchable" => false];

$datatableColumnsJson = json_encode($columns, JSON_UNESCAPED_UNICODE);

