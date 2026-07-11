<?php
///ipanel/controller/structure/company.php
use ipanel\model\StructureModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("company_name")));

//
$company_profilesResult = $structureModel->getCompanies();
//
$activitiesResultAdd = $structureModel->getActivities();
//
$activitiesResultEdit = $structureModel->getActivities();



