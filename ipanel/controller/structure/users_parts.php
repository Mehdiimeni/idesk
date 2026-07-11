<?php
///ipanel/controller/structure/users_parts.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("users_parts_name")));

//
$userPartsResult = $structureModel->getUserParts();
//
$userGroupsResult = $structureModel->getUserGroups();

