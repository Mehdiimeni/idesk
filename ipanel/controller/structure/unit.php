<?php
///ipanel/controller/structure/unit.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

/*
 * Unit management refresh
 * -----------------------
 * Compatibility rules:
 * - Existing units / company_profiles / users / admins / rbac / daily_reports tables are reused.
 * - No schema change, no new view/SP/event/index.
 * - Existing getUnits() remains untouched for legacy callers.
 * - CRUD is handled by a unit-specific protected endpoint.
 * - Delete is blocked whenever the unit has any business dependency.
 * - Moving an in-use unit to another company is blocked because user/admin company context is derived from unit_id.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['unit_csrf'])) {
    $_SESSION['unit_csrf'] = bin2hex(random_bytes(32));
}

$unitCsrf = $_SESSION['unit_csrf'];

$canAddUnit = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditUnit = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteUnit = (bool) $rbacClass->checkPermissionOperationByName('delete_operation');

$companyRows = [];
$companiesResult = $structureModel->getCompanies();
if ($companiesResult) {
    while ($row = $companiesResult->fetch_assoc()) {
        $companyRows[] = $row;
    }
    $companiesResult->free();
}

$unitRows = [];
$unitSummary = [
    'total' => 0,
    'companies' => 0,
    'members' => 0,
    'roles' => 0,
];
$unitCompanySet = [];

$unitsResult = $structureModel->getUnitsWithUsage();
if ($unitsResult) {
    while ($row = $unitsResult->fetch_assoc()) {
        $row['user_count'] = (int) ($row['user_count'] ?? 0);
        $row['admin_count'] = (int) ($row['admin_count'] ?? 0);
        $row['role_count'] = (int) ($row['role_count'] ?? 0);
        $row['daily_report_count'] = (int) ($row['daily_report_count'] ?? 0);
        $row['member_count'] = $row['user_count'] + $row['admin_count'];
        $row['known_dependency_count'] = $row['member_count'] + $row['role_count'] + $row['daily_report_count'];
        $row['is_in_use'] = $row['known_dependency_count'] > 0;

        $dateConverter = new DateConverter(
            $row['last_updated_date'],
            $config->getNowLanguage('a')
        );
        $row['last_updated_display'] = $dateConverter->convertToShamsi();

        $unitRows[] = $row;
        $unitSummary['total']++;
        $unitSummary['members'] += $row['member_count'];
        $unitSummary['roles'] += $row['role_count'];
        if (!empty($row['company_id'])) {
            $unitCompanySet[(int) $row['company_id']] = true;
        }
    }
    $unitsResult->free();
}

$unitSummary['companies'] = count($unitCompanySet);
