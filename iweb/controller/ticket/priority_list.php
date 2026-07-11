<?php
///controller/ticket/priority_list.php


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

use iweb\model\CustomerPriorityService;
use iweb\model\StructureModel;


$part_name = 'priority_list';


$service = new CustomerPriorityService($db);
$structureModel = new StructureModel($db);

$listOfTypeGroup = $service->findListsTypeGroup();

$textToolsClass = TextTools::getInstance();

// تعریف typeGroup: از GET یا اولی از لیست
$typeGroup = isset($_GET['type_group']) && !empty($_GET['type_group'])
    ? trim($_GET['type_group'])
    : ($listOfTypeGroup[0] ?? null);

// اگر type_group معتبر نیست
if (!$typeGroup || !in_array($typeGroup, $listOfTypeGroup, true)) {
    throw new \RuntimeException("Invalid or missing type_group");
}

$userId = (int) $_SESSION['user_id'];
$companyId = isset($_SESSION['company_id']) ? (int) $_SESSION['company_id'] : null;



$action = isset($_GET['action']) ? trim($_GET['action']) : 'view';

// پیام‌ها برای ویو
$flash = [
    'ok' => null,
    'err' => null
];

try {

    if ($action === 'add') {
        // افزودن تیکت به لیست
        $ticketId = (int) ($_POST['ticket_id'] ?? 0);
        $priority = (int) ($_POST['priority'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        $service->addTicketToList($userId, $typeGroup, $ticketId, $priority, $comment, $companyId);
        $flash['ok'] = _lang['ticket_added_success'] ?? "تیکت با موفقیت به لیست اضافه شد و لیست نیازمند تایید شد.";
    } elseif ($action === 'change_priority') {
        // تغییر اولویت
        $ticketId = (int) ($_POST['ticket_id'] ?? 0);
        $newPriority = (int) ($_POST['new_priority'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        $service->changeTicketPriority($userId, $typeGroup, $ticketId, $newPriority, $comment, $companyId);
        $flash['ok'] = _lang['priority_changed_success'] ?? "اولویت با موفقیت تغییر کرد و لیست نیازمند تایید شد.";
    } elseif ($action === 'remove') {
        // حذف دستی
        $ticketId = (int) ($_POST['ticket_id'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        $service->removeTicketManual($userId, $typeGroup, $ticketId, $comment, $companyId);
        $flash['ok'] = _lang['ticket_removed_success'] ?? "تیکت از لیست حذف شد و اولویت‌ها شیفت شدند.";
    }

    // --- داده‌ها برای View ---
    $list = $service->getOrCreateList($userId, $typeGroup, $companyId);
    $listId = (int) $list['id'];

    // پاکسازی تیکت‌های با وضعیت منع‌شده
    $service->cleanupExcludedTickets($listId, $structureModel);

    $items = $service->getListItems($listId);

    // دریافت تمام کامنت‌های لیست (نه آیتم‌ها)
    $listComments = $service->getListComments($listId);

    // تیکت‌های قابل انتخاب (برای افزودن)
    // اینجا مستقیم SQL می‌زنیم چون در کلاس سرویس هنوز متد لیست انتخابی ننوشته بودیم.
    // اگر خواستی، می‌برم داخل CustomerPriorityService.
    $excluded = $service->getExcludedConditions();

    // تیکت‌های قابل انتخاب (برای افزودن)
    $selectableTickets = [];

    if (!empty($excluded)) {
        // جایگزین امن برای IN (...)
        $placeholders = implode(',', array_fill(0, count($excluded), '?'));

        // نکته: ستون‌های tickets را ممکن است در سیستم شما متفاوت باشد (ticket_title, subject, ...)
        $sql = "
            SELECT t.ticket_id, t.ticket_number, t.ticket_title, t.ticket_status
            FROM grid_user_ticket_data t
            
            WHERE t.company_id = ?
              AND t.type_group = ?
              AND t.ticket_status NOT IN ($placeholders)
              AND t.ticket_id NOT IN (
                 SELECT pli.ticket_id
                 FROM priority_list_items pli
                 WHERE pli.priority_list_id = ?
              )
            ORDER BY t.ticket_id DESC
            LIMIT 200
        ";

        // Build types string for bind_param
        $types = 'is' . str_repeat('s', count($excluded)) . 'i';

        // آماده‌سازی references برای bind_param
        $params = array_merge([$companyId, $typeGroup], $excluded, [$listId]);
        $refs = [];
        foreach ($params as &$param) {
            $refs[] = &$param;
        }

        $st = $db->prepare($sql);
        if ($st) {
            call_user_func_array([$st, 'bind_param'], array_merge([$types], $refs));
            $st->execute();
            $result = $st->get_result();
            while ($row = $result->fetch_assoc()) {
                $selectableTickets[] = $row;
            }
            $result->free();
            $st->close();
        }
    } else {
        // اگر excluded conditions خالی است
        $sql = "
            SELECT t.ticket_id, t.ticket_number, t.ticket_title, t.ticket_status
            FROM grid_user_ticket_data t
            WHERE t.company_id = ?
              AND ty.type_group = ?
              AND t.id NOT IN (
                 SELECT pli.ticket_id
                 FROM priority_list_items pli
                 WHERE pli.priority_list_id = ?
              )
            ORDER BY t.ticket_id DESC
            LIMIT 200
        ";

        $st = $db->prepare($sql);
        if ($st) {
            $st->bind_param('isi', $companyId, $typeGroup, $listId);
            $st->execute();
            $result = $st->get_result();
            while ($row = $result->fetch_assoc()) {
                $selectableTickets[] = $row;
            }
            $result->free();
            $st->close();
        }
    }

} catch (Throwable $e) {
    $flash['err'] = $e->getMessage();

    // تلاش می‌کنیم با وجود خطا هم صفحه را بسازیم
    $list = $service->getOrCreateList($userId, $typeGroup, $companyId);
    $listId = (int) $list['id'];
    $items = $service->getListItems($listId);
    $selectableTickets = [];
}


