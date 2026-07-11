<?php

// web panel data table
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";
require_once "../class/encryptor.php";
require_once "../class/text_tools.php";
require_once "../class/jdatetime.php";
require_once "../class/date_converter.php";
require_once "../../iweb/model/StructureModel.php";
require_once "../../iweb/model/TicketModel.php";
require_once "../../iweb/model/UserModel.php";

use iweb\model\StructureModel;
use iweb\model\TicketModel;
use iweb\model\UserModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$user_language = isset($_COOKIE['user_language'])
    ? $_COOKIE['user_language']
    : $config->getConfig('defaultLanguage');

require_once "../lang/{$user_language}.php";
define('_lang', $config->getLang($user_language));

$userModel = new UserModel($db);
$rbacClass = new RBAC($db);
$encryptorClass = new Encryptor($config->getConfig('encryptWebKey'));
$ticketModel = new TicketModel($db);
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();

$part_name = 'tickets';

$condition_name = isset($_POST['condition']) ? $_POST['condition'] : '';
$details = isset($_POST['details']) ? $_POST['details'] : '';
$mark = isset($_POST['mark']) ? $_POST['mark'] : '';
$referred = isset($_POST['referred']) ? $_POST['referred'] : '';

$columns = [
    'ticket_id',
    'ticket_number',
    'type_group',
    'priority',
    'comment_count',
    'ticket_title',
    'ticket_status',
    'name',
    'creation_date',
    'type_name',
    'indicator_number',
    'ticket_description'
];

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';

if (!isset($columns[$orderColumnIndex])) {
    $orderColumnIndex = 0;
}

$orderColumn = $columns[$orderColumnIndex];

$searchWhere = '';

if ($searchValue !== '') {
    $escapedSearch = $db->real_escape_string($searchValue);
    $searchTerms = [];

    foreach ($columns as $col) {
        $searchTerms[] = "$col LIKE '%{$escapedSearch}%'";
    }

    $searchWhere = implode(" OR ", $searchTerms);
}

$limit = " LIMIT $start, $length";
$orderBy = " ORDER BY $orderColumn $orderDir";

$companyId = null;

if ($rbacClass->checkPermissionOperationByName('view_all_ticket_operation', 'u')) {
    $companyId = $_SESSION["company_id"];
}

$totalRecords = $ticketModel->getTicketCount(
    $condition_name,
    $companyId,
    $mark
);

$filteredRecords = $ticketModel->getTicketSearchCount(
    $condition_name,
    $companyId,
    $searchWhere,
    $mark
);

$allTicketsResult  = $ticketModel->getTicket(
    $condition_name,
    $companyId,
    $searchWhere,
    $limit,
    $orderBy,
    $mark
);

$allConditions = $structureModel->getConditionsByPart($part_name);
$typesResult = $structureModel->getTypes();

function getPriorityBadge($priority)
{
    if ($priority == 'low') {
        return '<span class="badge bg-primary">' . _lang['low'] . '</span>';
    }

    if ($priority == 'medium') {
        return '<span class="badge bg-warning">' . _lang['medium'] . '</span>';
    }

    if ($priority == 'high') {
        return '<span class="badge bg-danger">' . _lang['high'] . '</span>';
    }

    return '';
}

$permissionUserView = $rbacClass->checkPermissionOperationByName('view_operation', 'u');

$data = [];
$conditionCache = [];
$allTickets = $allTicketsResult->fetch_all(MYSQLI_ASSOC);
$ticketIds = array_column($allTickets, 'ticket_id');
$allMarkTags = $ticketModel->getMarkingTagsByTicketIds($ticketIds);

foreach ($allTickets as $row) {
    $rowJson = [];

    $ticketId = (int) $row['ticket_id'];

    $rowJson['ticket_id'] = $ticketId;
    $rowJson['ticket_number'] = $row['ticket_number'];

    $markTag = $allMarkTags[$row['ticket_id']] ?? null;
    if (isset($markTag['id'])) {
        $rowJson['ticket_number'] .=
            '<i class="ri-bookmark-fill text-warning" data-bs-toggle="tooltip" title="' .
            htmlspecialchars($markTag['marking_tag'], ENT_QUOTES, 'UTF-8') .
            '"></i>';
    }

    $dateConverter = new DateConverter($row['creation_date'], $config->getNowLanguage('a'));
    $rowJson['ticket_creation_date_shamsi'] = $dateConverter->convertToShamsi();

    $rowJson['type_group'] =
        '<span class="badge bg-soft-primary text-primary">' .
        htmlspecialchars($row['type_group'], ENT_QUOTES, 'UTF-8') .
        '</span>';

    $rowJson['priority'] = getPriorityBadge($row['priority']);
    $rowJson['ticket_comments'] = $row['comment_count'];

    $ticketTitle = $row['ticket_title'] ?? '';

    $rowJson['ticket_title_html'] =
        '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($ticketTitle, ENT_QUOTES, 'UTF-8') . '">' .
        (($details == 'long')
            ? htmlspecialchars($ticketTitle, ENT_QUOTES, 'UTF-8')
            : htmlspecialchars($textToolsClass->truncateText($ticketTitle, 75), ENT_QUOTES, 'UTF-8')) .
        '</span>';

    $ticketStatus = $row['ticket_status'];

    if (!isset($conditionCache[$ticketStatus])) {
        $conditionCache[$ticketStatus] = $structureModel->getConditionsByName($ticketStatus);
    }

    $condition = $conditionCache[$ticketStatus];

    $rowJson['ticket_status'] =
        '<span class="badge alert-' . $condition['condition_color'] . '">' .
        _lang[$condition['condition_name']] .
        '</span>';

    $rowJson['user_name_display'] = $row['name'];
    $rowJson['type_name_display'] = $row['type_name'];
    $rowJson['indicator_number_display'] = $row['indicator_number'] ?? '';

    $encrypted_ticket_id = $encryptorClass->encrypt($ticketId);

    if (!empty($referred)) {
        $ticketUrl = "./tickets?referred=" . urlencode($referred) . "&ticket_id=" . $encrypted_ticket_id;
    } elseif (!empty($condition_name)) {
        $ticketUrl = "./tickets?condition_name=" . urlencode($condition_name) . "&ticket_id=" . $encrypted_ticket_id;
    } else {
        $ticketUrl = "./tickets?ticket_id=" . $encrypted_ticket_id;
    }

    $actionsHtml = '<div class="d-flex gap-1 justify-content-end">';

    if ($permissionUserView) {
        $actionsHtml .= '
            <a href="' . htmlspecialchars($ticketUrl, ENT_QUOTES, 'UTF-8') . '" class="btn btn-xs btn-outline-primary rounded" title="مشاهده">
                <i class="ri-eye-line"></i>
            </a>';
    }

    if (($row['file_count'] ?? 0) > 0) {
        $actionsHtml .= '
            <span class="btn btn-xs btn-outline-success rounded" title="فایل پیوست">
                <i class="ri-attachment-2"></i>
            </span>';
    }

    $actionsHtml .= '</div>';

    $rowJson['actions_html'] = $actionsHtml;

    $data[] = $rowJson;
}

$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);

$db->close();

?>