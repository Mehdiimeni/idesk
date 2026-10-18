<?php
///ipanel/controller/structure/company.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

/*
 * Company management refresh
 * --------------------------
 * Compatibility rules:
 * - Existing company_profiles / activities / units / users / admins / rbac tables are reused.
 * - No schema change, no new view/SP/event/index.
 * - Existing getCompanies() remains untouched for legacy callers.
 * - CRUD is handled by a company-specific protected endpoint.
 * - Delete is blocked whenever the company has any business dependency.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['company_csrf'])) {
    $_SESSION['company_csrf'] = bin2hex(random_bytes(32));
}

$companyCsrf = $_SESSION['company_csrf'];

$canAddCompany = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditCompany = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteCompany = (bool) $rbacClass->checkPermissionOperationByName('delete_operation');

$activityRows = [];
$activitiesResult = $structureModel->getActivities();
if ($activitiesResult) {
    while ($row = $activitiesResult->fetch_assoc()) {
        $activityRows[] = $row;
    }
    $activitiesResult->free();
}

$companyRows = [];
$companySummary = [
    'total' => 0,
    'units' => 0,
    'members' => 0,
    'roles' => 0,
];

$companiesResult = $structureModel->getCompaniesWithUsage();
if ($companiesResult) {
    while ($row = $companiesResult->fetch_assoc()) {
        $row['unit_count'] = (int) ($row['unit_count'] ?? 0);
        $row['role_count'] = (int) ($row['role_count'] ?? 0);
        $row['user_count'] = (int) ($row['user_count'] ?? 0);
        $row['admin_count'] = (int) ($row['admin_count'] ?? 0);
        $row['member_count'] = $row['user_count'] + $row['admin_count'];
        $row['ticket_count'] = (int) ($row['ticket_count'] ?? 0);
        $row['is_in_use'] = (
            $row['unit_count'] > 0 ||
            $row['role_count'] > 0 ||
            $row['member_count'] > 0 ||
            $row['ticket_count'] > 0
        );

        $dateConverter = new DateConverter(
            $row['last_updated_date'],
            $config->getNowLanguage('a')
        );
        $row['last_updated_display'] = $dateConverter->convertToShamsi();

        $companyRows[] = $row;
        $companySummary['total']++;
        $companySummary['units'] += $row['unit_count'];
        $companySummary['members'] += $row['member_count'];
        $companySummary['roles'] += $row['role_count'];
    }
    $companiesResult->free();
}
