<?php
///ipanel/controller/structure/type.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

/*
 * Type management refresh
 * -----------------------
 * Compatibility rules:
 * - Existing types / tickets / priority_lists tables are reused.
 * - No schema change, no new table/view/SP/event/index.
 * - Legacy getTypes() and getTypesGroupedByTypeGroup() remain unchanged.
 * - CRUD is handled only by the protected type_manage.php endpoint.
 * - Delete is blocked for any direct type_id dependency.
 * - Changing type_group is blocked when it would reclassify existing tickets or orphan an active priority-list group.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['type_csrf'])) {
    $_SESSION['type_csrf'] = bin2hex(random_bytes(32));
}
$typeCsrf = $_SESSION['type_csrf'];

$canAddType = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditType = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteType = (bool) $rbacClass->checkPermissionOperationByName('delete_operation');

$typeRows = [];
$typeSummary = [
    'total' => 0,
    'groups' => 0,
    'used' => 0,
    'tickets' => 0,
];
$typeGroupSet = [];

$typesResult = $structureModel->getTypesWithUsage();
if ($typesResult) {
    while ($row = $typesResult->fetch_assoc()) {
        $row['ticket_count'] = (int) ($row['ticket_count'] ?? 0);
        $row['group_type_count'] = (int) ($row['group_type_count'] ?? 0);
        $row['priority_list_count'] = (int) ($row['priority_list_count'] ?? 0);
        $row['known_dependency_count'] = $row['ticket_count'];
        $row['is_in_use'] = $row['known_dependency_count'] > 0;

        // The group itself is protected if this is its last Type and priority lists already depend on that group name.
        $row['group_locked'] = $row['is_in_use'] || ($row['group_type_count'] <= 1 && $row['priority_list_count'] > 0);
        $row['delete_locked'] = $row['is_in_use'] || ($row['group_type_count'] <= 1 && $row['priority_list_count'] > 0);

        $dateConverter = new DateConverter(
            $row['last_updated_date'],
            $config->getNowLanguage('a')
        );
        $row['last_updated_display'] = $dateConverter->convertToShamsi();

        $typeRows[] = $row;
        $typeSummary['total']++;
        $typeSummary['tickets'] += $row['ticket_count'];
        if ($row['is_in_use']) {
            $typeSummary['used']++;
        }
        if (trim((string) ($row['type_group'] ?? '')) !== '') {
            $typeGroupSet[(string) $row['type_group']] = true;
        }
    }
    $typesResult->free();
}

$typeSummary['groups'] = count($typeGroupSet);
$typeGroupNames = array_keys($typeGroupSet);
natcasesort($typeGroupNames);
$typeGroupNames = array_values($typeGroupNames);
