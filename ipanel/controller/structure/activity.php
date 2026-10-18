<?php
///ipanel/controller/structure/activity.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$unique_fields = base64_encode(serialize(array("activity_name")));

/*
 * Activity management refresh
 * ---------------------------
 * Compatibility rules:
 * - Existing activities/company_profiles/rbac tables are reused.
 * - No schema change, no view, no SP, no event and no new index.
 * - Existing getActivities() remains untouched for all legacy callers.
 * - This page reads activity usage in one query and moves CRUD to a
 *   page-specific protected endpoint instead of the generic table endpoint.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['activity_csrf'])) {
    $_SESSION['activity_csrf'] = bin2hex(random_bytes(32));
}

$activityCsrf = $_SESSION['activity_csrf'];

$canAddActivity = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditActivity = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteActivity = (bool) $rbacClass->checkPermissionOperationByName('delete_operation');

$activityRows = [];
$activitySummary = [
    'total' => 0,
    'in_use' => 0,
    'companies' => 0,
    'roles' => 0,
];

$activitiesResult = $structureModel->getActivitiesWithUsage();
if ($activitiesResult) {
    while ($row = $activitiesResult->fetch_assoc()) {
        $row['company_count'] = (int) ($row['company_count'] ?? 0);
        $row['role_count'] = (int) ($row['role_count'] ?? 0);
        $row['is_in_use'] = ($row['company_count'] > 0 || $row['role_count'] > 0);

        $dateConverter = new DateConverter(
            $row['last_updated_date'],
            $config->getNowLanguage('a')
        );
        $row['last_updated_display'] = $dateConverter->convertToShamsi();

        $activityRows[] = $row;
        $activitySummary['total']++;
        $activitySummary['companies'] += $row['company_count'];
        $activitySummary['roles'] += $row['role_count'];
        if ($row['is_in_use']) {
            $activitySummary['in_use']++;
        }
    }
    $activitiesResult->free();
}
