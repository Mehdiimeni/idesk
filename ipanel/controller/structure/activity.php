<?php
///ipanel/controller/structure/activity.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("activity_name")));

