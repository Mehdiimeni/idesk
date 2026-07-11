<?php
///controller/ticket/priority_list_details.php
use ipanel\model\AdminPriorityService;
use ipanel\model\StructureModel;
use ipanel\model\TicketModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$service = new AdminPriorityService($db);
$structureModel = new StructureModel($db);

$textToolsClass = TextTools::getInstance();
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));
$ticketModel = new TicketModel($db);
// auth
if (!isset($_SESSION['admin_id'])) {
    throw new \RuntimeException("Unauthorized");
}
$adminId = (int) $_SESSION['admin_id'];

$flash = ['ok' => null, 'err' => null];

try {

    // عملیات
    if (isset($_POST['action'])) {
        $action = trim($_POST['action']);

        if ($action === 'approve') {
            $listId = (int) ($_POST['list_id'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');

            if ($listId <= 0)
                throw new \RuntimeException("list_id required");

            $service->approveList($adminId, $listId, $comment);
            $flash['ok'] = _lang['list_approved'] ?? "لیست تایید شد.";
        } elseif ($action === 'reject') {
            $listId = (int) ($_POST['list_id'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');

            if ($listId <= 0)
                throw new \RuntimeException("list_id required");
            $service->rejectList($adminId, $listId, $comment);

            $flash['ok'] = _lang['list_rejected'] ?? "لیست رد شد.";
        } elseif ($action === 'change_priority') {
            $listId = (int) ($_POST['list_id'] ?? 0);
            $ticketId = (int) ($_POST['ticket_id'] ?? 0);
            $newPriority = (int) ($_POST['new_priority'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');

            if ($listId <= 0 || $ticketId <= 0 || $newPriority <= 0)
                throw new \RuntimeException("Invalid parameters");

            $service->changeTicketPriorityByAdmin($adminId, $listId, $ticketId, $newPriority, $comment);

            $flash['ok'] = _lang['priority_changed_success'] ?? "اولویت تغییر کرد.";
        } elseif ($action === 'remove') {
            $listId = (int) ($_POST['list_id'] ?? 0);
            $ticketId = (int) ($_POST['ticket_id'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');

            if ($listId <= 0 || $ticketId <= 0)
                throw new \RuntimeException("Invalid parameters");

            $service->removeTicketByAdmin($adminId, $listId, $ticketId, $comment);

            $flash['ok'] = _lang['ticket_removed_success'] ?? "تیکت حذف شد.";
        }
    }

    // Get details
    $listId = (int) ($_GET['list_id'] ?? $_POST['list_id'] ?? 0);
    if ($listId <= 0)
        throw new \RuntimeException("list_id required");

    $list = $service->getListById($listId);
    if (empty($list))
        throw new \RuntimeException("List not found");

    $items = $service->getListItems($listId);
    $companyName = $service->getCompanyNameFromList($listId);

} catch (Throwable $e) {
    $flash['err'] = $e->getMessage();
    $list = null;
    $items = [];
    $companyName = null;
}
