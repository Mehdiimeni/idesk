<?php
///ipanel/controller/ticket/tickets.php
use ipanel\model\StructureModel;
use ipanel\model\TicketModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));
$ticketModel = new TicketModel($db);

$part_name = 'tickets';
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();

$condition_name = '';
if (isset($_GET['condition_name']) && $_GET['condition_name'] != '') {
    $condition_name = $_GET['condition_name'];
}


$isEntry = $rbacClass->checkPermissionOperationByName('pointer_operation') ? 1 : 0;


//load data

$allValueLoad = [50, 100, 200, 500, 1000, 2500, 100000];

// دریافت مقادیر از URL
$getRequestLoad = $_GET['load'] ?? null;
$getRequestReferred = $_GET['referred'] ?? null;

// تعیین مقدار نهایی load
if ($getRequestReferred !== null && $getRequestReferred !== '') {
    // وقتی referred وجود دارد، همیشه همه آیتم‌ها نمایش داده شوند
    $setValue = end($allValueLoad);
} else {
    // اگر load خالی بود، مقدار پیش‌فرض
    if ($getRequestLoad === null || $getRequestLoad === '') {
        $setValue = $allValueLoad[0]; // 50

    } else {
        $intValue = (int) $getRequestLoad;

        if (in_array($intValue, $allValueLoad, true)) {
            $setValue = $intValue;
        } else {
            $setValue = end($allValueLoad);
        }
    }
}

// متن دکمه اصلی
$buttonText = ($setValue === end($allValueLoad))
    ? _lang['show_all']
    : $setValue . ' ' . _lang['last_items'];

// ساخت آیتم‌های dropdown
$viewValueSelect = '';

foreach ($allValueLoad as $valueLoad) {
    $isActive = ((string) $valueLoad === (string) $setValue) ? 'active' : '';

    $strCaption = ($valueLoad === end($allValueLoad))
        ? _lang['show_all']
        : $valueLoad . ' ' . _lang['last_items'];

    // اگر referred در URL باشد، در لینک‌ها هم حفظ شود
    $queryParams = [];

    if ($valueLoad !== end($allValueLoad)) {
        $queryParams['load'] = $valueLoad;
    } else {
        $queryParams['load'] = end($allValueLoad);
    }

    if ($getRequestReferred !== null && $getRequestReferred !== '') {
        $queryParams['referred'] = $getRequestReferred;
    }

    if (isset($_GET['mark']) && $_GET['mark'] !== '') {
        $queryParams['mark'] = $_GET['mark'];
    }

    $href = './tickets';
    if (!empty($queryParams)) {
        $href .= '?' . http_build_query($queryParams);
    }



    $viewValueSelect .= sprintf(
        '<a class="dropdown-item %s" href="%s" data-load="%s">%s</a>',
        $isActive,
        htmlspecialchars($href, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars((string) $valueLoad, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($strCaption, ENT_QUOTES, 'UTF-8')
    );
}

// تنظیم تعداد نمایش در مدل
$ticketModel->setTicketGridView($setValue);

// دریافت تیکت‌ها
if ($isEntry) {

    if (isset($_GET['referred']) && $_GET['referred'] == '1') {
        $allTickets = $ticketModel->getAllAdminForwardTickets(
            $_SESSION['admin_id'],
            $condition_name
        );

    } elseif (isset($_GET['referred']) && $_GET['referred'] == '0') {
        $allTickets = $ticketModel->getAllAdminNoActionTicketsWithExcept(
            $_SESSION['admin_id'],
            ['condition_archive', 'condition_final_done', 'condition_pendency']
        );

    } else {
        $mark_id = $_GET['mark'] ?? '';
        $limit = $_GET['limit'] ?? null;
        $start = $_GET['start'] ?? null;
        $allTickets = $ticketModel->getAllTickets($condition_name, $mark_id);

    }

} else {

    if (isset($_GET['referred']) && $_GET['referred'] == '1') {
        $allTickets = $ticketModel->getAllAdminForwardTickets(
            $_SESSION['admin_id'],
            $condition_name
        );

    } elseif (isset($_GET['referred']) && $_GET['referred'] == '0') {
        $allTickets = $ticketModel->getAllAdminNoActionTickets(
            $_SESSION['admin_id'],
            $condition_name
        );

    } else {
        $allTickets = $ticketModel->getAllAdminNoActionTickets(
            $_SESSION['admin_id'],
            $condition_name
        );
    }
}




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

// marks list

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

// show ticket mark
if ($rbacClass->checkPermissionOperationByName('view_ticket_mark_operation')) {
    $permissionViewMark = true;
} else {
    $permissionViewMark = false;
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




