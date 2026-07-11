<?php
///ipanel/controller/structure/user_view_details.php
use ipanel\model\StructureModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$permission_data = $rbacClass->getPermissionById($_GET['id']);
$rbac_data = $structureModel->getRBACByIdWithCompany(rbac_id: $permission_data['rbac_id']);

$unique_fields = base64_encode(serialize(array("")));

