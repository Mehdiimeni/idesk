<?php
///ipanel/controller/ticket/kanban_board.php
use ipanel\model\AdminModel;
use ipanel\model\TicketModel;
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$ticketModel = new TicketModel($db);
$rbacClass = new RBAC($db);
$structureModel = new StructureModel($db);
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));


$unique_fields = base64_encode(serialize(array("last_updated_date")));

$part_name = 'tickets';
$textToolsClass = TextTools::getInstance();

// all kanban
$allKanbanTag = $ticketModel->getAllKabanTag();

// make task list
$countKanbanTag = $ticketModel->getCountKabanTag();
$tasks = [];
for ($i = 1; $i <= $countKanbanTag[0]["count_id"]; $i++) {
    $tasks[] = "task-list-" . $i;
}

$allListTaskId = json_encode($tasks, JSON_UNESCAPED_SLASHES);
