<?php
///ipanel/controller/structure/unit.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("unit_name")));

//
$unitsResult = $structureModel->getUnits();
//
$companiesResult = $structureModel->getCompanies();

