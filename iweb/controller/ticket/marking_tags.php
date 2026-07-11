<?php
///controller/ticket/marking_tags.php
use iweb\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();

$unique_fields = base64_encode(serialize(array("")));

$userId = $_SESSION['user_id'];
$companyId = $_SESSION['company_id'];

//
$markingTagsResult = $structureModel->getUserMarkingTags($userId, $companyId);