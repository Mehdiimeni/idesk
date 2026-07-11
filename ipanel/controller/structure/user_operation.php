<?php
///controller/structure/user_operation.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("rbac_id")));

//
$userOperationResult = $structureModel->getUserOperation();
//
$rbacResult = $structureModel->getRBAC(1);
//
$groupResult = $structureModel->getUserGroups();
//
$adminGroupResult = $structureModel->getAdminGroups();
//
$operationResult = $structureModel->getOperations();
