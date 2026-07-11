<?php
///ipanel/controller/structure/user_operation_details.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$permission_data = $rbacClass->getPermissionById($_GET['id']);
$rbac_data = $structureModel->getRBACByIdWithCompany($permission_data['rbac_id']);

$unique_fields = base64_encode(serialize(array("rbac_id")));

