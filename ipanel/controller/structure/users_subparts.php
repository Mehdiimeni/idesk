<?php
///ipanel/controller/structure/users_subparts.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("users_subparts_name")));

//
$userSubPartsResult = $structureModel->getUserSubParts();
//
$userPartsResult = $structureModel->getUserParts();
//
