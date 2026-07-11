<?php
///ipanel/controller/structure/admins_parts.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("admins_parts_name")));

//
$adminPartsResult = $structureModel->getadminParts();
//
$adminGroupsResult = $structureModel->getAdminGroups();
//
$adminGroupsResult = $structureModel->getAdminGroups();
