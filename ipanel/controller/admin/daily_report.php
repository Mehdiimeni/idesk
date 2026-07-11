<?php
///ipanel/controller/admin/daily_report.php
use ipanel\model\AdminModel;
use ipanel\model\ManagerialModel;
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$rbacClass = new RBAC($db);
$adminManagerialModel = new ManagerialModel($db);


$part_name = 'daily_report';
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();

$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));
$allDailyReport = $adminManagerialModel->getAllDailyReport($_SESSION['admin_id']);
$dailyReportCounts = $adminManagerialModel->getDailyReportCountByUsers($_SESSION['admin_id']);