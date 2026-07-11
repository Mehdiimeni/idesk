<?php
///ipanel/controller/member/users.php
use ipanel\model\StructureModel;
use iweb\model\UserModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
$usersResult = $userModel->getUsers();


$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);


$unique_fields = base64_encode(serialize(array("email", "mobile", "name")));