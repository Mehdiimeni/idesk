<?php
///ipanel/controller/structure/user_stracture.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("")));

//
$userStractureResult = $structureModel->getUserStracture();
//
$rbacResult = $structureModel->getRBACNotInStracture(1);
//
$activityResult = $structureModel->getActivities();
