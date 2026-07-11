<?php
///ipanel/controller/member/admins.php
use ipanel\model\AdminModel;
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);


$unique_fields = base64_encode(serialize( array("email","mobile","name")));