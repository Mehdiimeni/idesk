<?php
///controller/ticket/marking_tags.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("")));

$adminId = $_SESSION['admin_id'];
$companyId = $_SESSION['company_id'];

//
$markingTagsResult = $structureModel->getAdminMarkingTags($adminId,$companyId);