<?php
///iweb/controller/user/chat_center.php
use iweb\model\StructureModel;
use iweb\model\UserChatModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$userChatModel = new UserChatModel($db);
$companies = $structureModel->getAllCompaniesMembersUser($_SESSION["company_id"]);

$showChatBox = false;
if (isset($_GET['rId']) && isset($_GET['rt'])) {

    $showChatBox = true;
    $receiver_id = $_GET['rId'];
    $receiver_type = $_GET['rt'];
    $receiverDetials = $structureModel->getCompaniesMemberById($receiver_id, $receiver_type)[0];
    $userChatModel->setChatViewSide($_SESSION['user_id'], 'u', $receiverDetials['member_id'], $receiverDetials['member_type']);
}