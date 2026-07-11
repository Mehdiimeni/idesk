<?php

use iweb\model\NotificationModel;
///controller/global/page_top.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$allLanguages = $config->getConfig('allLanguage');

$textToolsClass = TextTools::getInstance();

$notificationModel = new NotificationModel($db);
$rbacClass = new RBAC($db);

$allNewTicketsNoteUser = $notificationModel->noteCommentTicket(0,0,0,2);
$encryptorClass = new Encryptor($config->getConfig('encryptWebKey'));
$allNewChatMessages = $notificationModel->noteNewChatMessages($_SESSION['user_id'],'u',2);


$totalNote = count($allNewChatMessages) +$allNewTicketsNoteUser->num_rows;
$totalChatNote = count($allNewChatMessages);
