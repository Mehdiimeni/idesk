<?php

// admin panel data table
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";
require_once "../class/encryptor.php";
require_once "../class/text_tools.php";
require_once "../class/jdatetime.php";
require_once "../class/date_converter.php";
require_once "../../ipanel/model/StructureModel.php";
require_once "../../ipanel/model/TicketModel.php";

use ipanel\model\StructureModel;
use ipanel\model\TicketModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin_language = isset($_COOKIE['admin_language'])
    ? $_COOKIE['admin_language']
    : $config->getConfig('defaultLanguage');

require_once "../lang/{$admin_language}.php";
define('_lang', $config->getLang($admin_language));

$rbacClass = new RBAC($db);
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));
$ticketModel = new TicketModel($db);
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();

$part_name = 'tickets';

$mark = isset($_POST['mark']) ? $_POST['mark'] : '';
$referred = isset($_POST['referred']) ? $_POST['referred'] : '';
$condition_name = isset($_POST['condition']) ? $_POST['condition'] : '';
$details = isset($_POST['details']) ? $_POST['details'] : '';

$isEntry = $rbacClass->checkPermissionOperationByName('pointer_operation') ? 1 : 0;

$columns = [
    'ticket_id',
    'ticket_number',
    'type_group',
    'ticket_priority',
    'comment_count',
    'ticket_title',
    'ticket_status',
    'ticket_status',
    'company_name',
    'user_name',
    'last_receiver_name',
    'ticket_creation_date',
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

$totalRecords = 0;
$filteredRecords = 0;
$allTicketsResult = [];

if ($isEntry) {
    if ($referred == '1') {
        $totalRecords = $ticketModel->getAllAdminForwardTicketsCount(
            $_SESSION['admin_id'],
            $condition_name,
            $mark
        );

        $filteredRecords = $ticketModel->getAllAdminForwardTicketsSearchCount(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $mark
        );

        $allTicketsResult = $ticketModel->getAllAdminForwardTickets(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $limit,
            $orderBy,
            $mark
        );
    } elseif ($referred == '0') {
        $exceptConditions = [
            'condition_archive',
            'condition_final_done',
            'condition_pendency'
        ];

        $totalRecords = $ticketModel->getAllAdminNoActionTicketsWithExceptCount(
            $_SESSION['admin_id'],
            $exceptConditions,
            $mark
        );

        $filteredRecords = $ticketModel->getAllAdminNoActionTicketsWithExceptSearchCount(
            $_SESSION['admin_id'],
            $exceptConditions,
            $searchWhere,
            $mark
        );

        $allTicketsResult = $ticketModel->getAllAdminNoActionTicketsWithExcept(
            $_SESSION['admin_id'],
            $exceptConditions,
            $searchWhere,
            $limit,
            $orderBy,
            $mark
        );
    } else {
        $totalRecords = $ticketModel->getAllTicketsCount($condition_name, $mark);

        $filteredRecords = $ticketModel->getAllTicketsSearchCount(
            $condition_name,
            $mark,
            $searchWhere
        );

        $allTicketsResult = $ticketModel->getAllTickets(
            $condition_name,
            $mark,
            $searchWhere,
            $limit,
            $orderBy
        );
    }
} else {
    if ($referred == '1') {
        $totalRecords = $ticketModel->getAllAdminForwardTicketsCount(
            $_SESSION['admin_id'],
            $condition_name,
            $mark
        );

        $filteredRecords = $ticketModel->getAllAdminForwardTicketsSearchCount(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $mark
        );

        $allTicketsResult = $ticketModel->getAllAdminForwardTickets(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $limit,
            $orderBy,
            $mark
        );
    } elseif ($referred == '0') {
        $totalRecords = $ticketModel->getAllAdminNoActionTicketsCount(
            $_SESSION['admin_id'],
            $condition_name,
            $mark
        );

        $filteredRecords = $ticketModel->getAllAdminNoActionTicketsSearchCount(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $mark
        );

        $allTicketsResult = $ticketModel->getAllAdminNoActionTickets(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $limit,
            $orderBy,
            $mark
        );
    } else {
        $totalRecords = $ticketModel->getAllAdminNoActionTicketsCount(
            $_SESSION['admin_id'],
            $condition_name,
            $mark
        );

        $filteredRecords = $ticketModel->getAllAdminNoActionTicketsSearchCount(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $mark
        );

        $allTicketsResult = $ticketModel->getAllAdminNoActionTickets(
            $_SESSION['admin_id'],
            $condition_name,
            $searchWhere,
            $limit,
            $orderBy,
            $mark
        );
    }
}

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

$permissionTableFilter = false;

if ($rbacClass->checkPermissionOperationByName('table_filter_operation')) {
    $permissionTableFilter = true;
    $company_profilesResult = $structureModel->getCompanies();
    $unitsResult = $structureModel->getUnits();
    $typesResult = $structureModel->getTypes();
    $allConditions = $structureModel->getConditionsByPart($part_name);
}

$permissionViewLocation = $rbacClass->checkPermissionOperationByName('view_location_operation') || ($referred == 1);
$permissionView = $rbacClass->checkPermissionOperationByName('view_operation');
$permissionViewBeforeStatus = $rbacClass->checkPermissionOperationByName('view_before_status_operation');
$permissionAddTicket = $rbacClass->checkPermissionOperationByName('add_ticket_operation');
$permissionViewIndicatorNumbert = $rbacClass->checkPermissionOperationByName('view_indicator_number_operation');

$permissionViewWorkflowPart = $rbacClass->checkPermissionPartByName('workflow', 'workflow');
$permissionViewManHourPart = $rbacClass->checkPermissionPartByName('man_hour', 'workflow');
$permissionViewFileManagerPart = $rbacClass->checkPermissionPartByName('file_manager', 'workflow');

$data = [];
$conditionCache = [];
$ticketIds = array_column($allTicketsResult, 'ticket_id');
$allMarkTags = $ticketModel->getMarkingTagsByTicketIds($ticketIds);
foreach ($allTicketsResult as $row) {
    $rowJson = [];

    if ($permissionViewBeforeStatus) {
        $beforeStatus = (isset($row['before_status_name']) && $row['before_status_name'] != '')
            ? _lang['condition_backward'] . " : " . _lang[strtolower($row['before_status_name'])] . ' - ' . $row['before_person_name'] . " ( " . $row['status_description'] . " ) "
            : '';
    } else {
        $beforeStatus = '';
    }

    $ticketId = (int) $row['ticket_id'];

    $lastFinanceStatus = $ticketModel->getLastFinanceStatus($ticketId, 'tickets');
    $lastNonFinanceStatus = $ticketModel->getLastNonFinanceStatus($ticketId, 'tickets');

    $rowJson['ticket_id'] = $ticketId;
    $rowJson['ticket_number'] = $row['ticket_number'];

    $markTag = $allMarkTags[$row['ticket_id']] ?? null;

    if (isset($markTag['id'])) {
        $rowJson['ticket_number'] .= '<i class="ri-bookmark-fill text-warning" data-bs-toggle="tooltip" title="' . htmlspecialchars($markTag['marking_tag'], ENT_QUOTES, 'UTF-8') . '"></i>';
    }

    $dateConverter = new DateConverter($row['ticket_creation_date'], $config->getNowLanguage('a'));
    $rowJson['ticket_creation_date_shamsi'] = $dateConverter->convertToShamsi();

    $rowJson['type_group'] = '<span class="badge bg-soft-primary text-primary">' . htmlspecialchars($row['type_group'], ENT_QUOTES, 'UTF-8') . '</span>';
    $rowJson['ticket_priority'] = getPriorityBadge($row['ticket_priority']);

    $rowJson['ticket_comments'] = $row['comment_count'];

    if ($row['comment_local'] > 0) {
        $rowJson['ticket_comments'] .= '<span class="badge bg-warning rounded-circle p-1">' . (int) $row['comment_local'] . '</span>';
    }

    $ticketTitle = $row['ticket_title'] ?? '';

    $rowJson['ticket_title_html'] =
        '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($ticketTitle, ENT_QUOTES, 'UTF-8') . '">'
        . (($details == 'long')
            ? htmlspecialchars($ticketTitle, ENT_QUOTES, 'UTF-8')
            : htmlspecialchars($textToolsClass->truncateText($ticketTitle, 75), ENT_QUOTES, 'UTF-8'))
        . '</span>';

    if (count($lastNonFinanceStatus) > 0) {
        $statusName = $lastNonFinanceStatus[0]["status_name"];

        if (!isset($conditionCache[$statusName])) {
            $conditionCache[$statusName] = $structureModel->getConditionsByName($statusName);
        }

        $conditionNonFinance = $conditionCache[$statusName];

        $rowJson['non_finance_status_html'] =
            '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($beforeStatus, ENT_QUOTES, 'UTF-8') . '">'
            . '<span class="badge alert-' . $conditionNonFinance['condition_color'] . '">'
            . _lang[$conditionNonFinance['condition_name']]
            . '</span>'
            . '</span>';
    } else {
        $rowJson['non_finance_status_html'] =
            '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($beforeStatus, ENT_QUOTES, 'UTF-8') . '"></span>';
    }

    if (count($lastFinanceStatus) > 0) {
        $statusName = $lastFinanceStatus[0]["status_name"];

        if (!isset($conditionCache[$statusName])) {
            $conditionCache[$statusName] = $structureModel->getConditionsByName($statusName);
        }

        $conditionFinance = $conditionCache[$statusName];

        $rowJson['finance_status_html'] =
            '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($beforeStatus, ENT_QUOTES, 'UTF-8') . '">'
            . '<span class="badge alert-' . $conditionFinance['condition_color'] . '">'
            . _lang[$conditionFinance['condition_name']]
            . '</span>'
            . '</span>';
    } else {
        $rowJson['finance_status_html'] =
            '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($beforeStatus, ENT_QUOTES, 'UTF-8') . '"></span>';
    }

    $companyName = $row['company_name'] ?? '';

    $rowJson['company_name_html'] =
        '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') . '">'
        . (($details == 'long')
            ? htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8')
            : htmlspecialchars($textToolsClass->truncateText($companyName, 55), ENT_QUOTES, 'UTF-8'))
        . '</span>';

    $rowJson['user_name_display'] = $row['user_name'];

    if ($permissionViewLocation) {
        $rowJson['last_receiver_name_display'] = $row['last_receiver_name'];
    }

    $rowJson['type_name_display'] = $row['type_name'];

    if ($permissionAddTicket || $permissionViewIndicatorNumbert) {
        $rowJson['indicator_number_display'] = $row['indicator_number'] ?? '';
    } else {
        $rowJson['indicator_number_display'] = '';
    }

    $encrypted_ticket_id = $encryptorClass->encrypt($ticketId);

    if (!empty($referred)) {
        $ticketUrl = "./tickets?referred=" . urlencode($referred) . "&ticket_id=" . $encrypted_ticket_id;
    } elseif (!empty($condition_name)) {
        $ticketUrl = "./tickets?condition_name=" . urlencode($condition_name) . "&ticket_id=" . $encrypted_ticket_id;
    } else {
        $ticketUrl = "./tickets?ticket_id=" . $encrypted_ticket_id;
    }

    $actionsHtml = '<div class="d-flex gap-1 justify-content-end">';

    if ($permissionView) {
        $actionsHtml .= '
            <a href="' . htmlspecialchars($ticketUrl, ENT_QUOTES, 'UTF-8') . '" class="btn btn-xs btn-outline-primary rounded" title="مشاهده">
                <i class="ri-eye-line"></i>
            </a>';
    }

    if ($permissionViewWorkflowPart && ($row['count_forward'] ?? 0) > 0) {
        $actionsHtml .= '
            <a href="./workflow?ticket_number=' . urlencode($row['ticket_number']) . '" class="btn btn-xs btn-outline-info rounded" title="' . (int) $row['count_forward'] . '">
                <i class="ri-flow-chart"></i>
            </a>';
    }

    if ($permissionViewManHourPart && ($row['man_hour_count'] ?? 0) > 0) {
        $actionsHtml .= '
            <a href="./man_hour?ticket_number=' . urlencode($row['ticket_number']) . '" class="btn btn-xs btn-outline-warning rounded" title="' . (int) $row['man_hour_count'] . '">
                <i class="ri-time-line"></i>
            </a>';
    }

    if (($row['file_count'] ?? 0) > 0) {
        if ($permissionViewFileManagerPart) {
            $actionsHtml .= '
                <a href="./file_manager?ticket_number=' . urlencode($row['ticket_number']) . '" class="btn btn-xs btn-outline-success rounded" title="' . (int) $row['file_count'] . '">
                    <i class="ri-attachment-2"></i>
                </a>';
        } else {
            $actionsHtml .= '
                <span class="btn btn-xs btn-outline-success rounded">
                    <i class="ri-attachment-2"></i>
                </span>';
        }
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