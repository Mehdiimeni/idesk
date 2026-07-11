<?php
///ipanel/controller/structure/admins_subparts.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("admins_subparts_name")));

//
$adminsubPartsResult = $structureModel->getadminsubParts();
//
$adminPartsResult = $structureModel->getAdminParts();
//
$adminPartsResult = $structureModel->getAdminParts();
