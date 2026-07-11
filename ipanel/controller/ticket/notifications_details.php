<?php
///ipanel/controller/ticket/notifications_details.php
use ipanel\model\NotificationModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);

$notificationModel = new NotificationModel($db);


$important = isset($_GET['filter']) && $_GET['filter'] == 'imp' ? 1 : 0; 
$sent = isset($_GET['filter']) && $_GET['filter'] == 'sm' ? 1 : 0;

$allForwarderTicket = $notificationModel->noteForwarderTicket(0);

$allMessages = $notificationModel->noteMessages( $important, $sent);

$messageDetails = [];

// unique fields null
$unique_fields = base64_encode(serialize(array("")));

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $id = intval($_GET['id']); 
    $messageDetails = $notificationModel->getMessageDetails($id);

    if (!empty($messageDetails)) {
        $notificationModel->setViewMessage($id);
    }
}
