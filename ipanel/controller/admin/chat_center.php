<?php
///ipanel/controller/admin/chat_center.php
use ipanel\model\ChatModel;
use ipanel\model\StructureModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();


$adminChatModel = new ChatModel($db);
$structureModel = new StructureModel($db);

$companies = $structureModel->getAllCompaniesMembers();

$showChatBox = false;
if (isset($_GET['rId']) && isset($_GET['rt'])) {
    $showChatBox = true;
    $receiver_id = $_GET['rId'];
    $receiver_type = $_GET['rt'];
    $receiverDetials = $structureModel->getCompaniesMemberById($receiver_id, $receiver_type)[0];
    $adminChatModel->setChatViewSide($_SESSION['admin_id'], 'a', $receiverDetials['member_id'], $receiverDetials['member_type']);
}