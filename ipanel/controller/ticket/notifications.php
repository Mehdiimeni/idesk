<?php
///ipanel/controller/ticket/notifications.php
use ipanel\model\NotificationModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("")));


$textToolsClass = TextTools::getInstance();
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));


$notificationModel = new NotificationModel($db);
$allForwarderTicket = $notificationModel->noteForwarderTicket(0);

$important = isset($_GET['filter']) && $_GET['filter'] == 'imp' ? 1 : 0;
$sent = isset($_GET['filter']) && $_GET['filter'] == 'sm' ? 1 : 0;
$allMessages = $notificationModel->noteMessages($important,$sent);

$allNewTicketsComment = $notificationModel->noteCommentTicket(0);


$currentFilter = $_GET['filter'] ?? 'imp';

function activeNotificationMenu($filter, $currentFilter)
{
    return $filter === $currentFilter ? 'text-danger fw-bold' : '';
}




