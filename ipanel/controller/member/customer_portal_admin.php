<?php

use iweb\model\CustomerPortalService;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$customerPortalService = new CustomerPortalService($db);
$rbacClass = new RBAC($db);

if (!function_exists('cpAdminCsrf')) {
    function cpAdminCsrf(): string
    {
        if (empty($_SESSION['cp_admin_csrf'])) {
            $_SESSION['cp_admin_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['cp_admin_csrf'];
    }
}
if (!function_exists('cpAdminRedirect')) {
    function cpAdminRedirect(int $companyId, string $type, string $message): void
    {
        $_SESSION['cp_admin_flash'] = ['type'=>$type,'message'=>$message];
        header('Location: ./customer_portal_admin?company_id=' . $companyId);
        exit;
    }
}

$cpAdminCsrf = cpAdminCsrf();
$cpAdminFlash = $_SESSION['cp_admin_flash'] ?? null;
unset($_SESSION['cp_admin_flash']);
$adminId = (int) ($_SESSION['admin_id'] ?? 0);
$canEditPortalAdmin = $rbacClass->checkPermissionOperationByName('edit_operation');
$cpCompanies = $customerPortalService->listCompaniesForAdmin();
$selectedCompanyId = (int) ($_GET['company_id'] ?? 0);
if ($selectedCompanyId <= 0 && $cpCompanies) {
    $selectedCompanyId = (int) $cpCompanies[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCompanyId = (int) ($_POST['company_id'] ?? $selectedCompanyId);
    if (!$canEditPortalAdmin) {
        cpAdminRedirect($selectedCompanyId, 'danger', 'EDIT_PERMISSION_REQUIRED');
    }
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['cp_admin_csrf'] ?? '', (string) $_POST['csrf_token'])) {
        cpAdminRedirect($selectedCompanyId, 'danger', 'CSRF_INVALID');
    }
    try {
        $action = (string) ($_POST['cp_admin_action'] ?? '');
        if ($action === 'save_settings') {
            $customerPortalService->saveCompanySettingsByAdmin($adminId, $selectedCompanyId, [
                'customer_portal_workflow_enabled' => $_POST['customer_portal_workflow_enabled'] ?? 0,
                'portal_unit_id' => $_POST['portal_unit_id'] ?? null,
                'admin_rbac_id' => $_POST['admin_rbac_id'] ?? null,
                'manager_rbac_id' => $_POST['manager_rbac_id'] ?? null,
                'expert_rbac_id' => $_POST['expert_rbac_id'] ?? null,
                'draft_aging_days' => $_POST['draft_aging_days'] ?? 14,
            ]);
            cpAdminRedirect($selectedCompanyId, 'success', 'PORTAL_SETTINGS_SAVED');
        }
        if ($action === 'set_permission') {
            $customerPortalService->setPermissionByAdmin(
                $adminId,
                (int) ($_POST['user_id'] ?? 0),
                (string) ($_POST['permission_name'] ?? ''),
                !empty($_POST['is_allowed'])
            );
            cpAdminRedirect($selectedCompanyId, 'success', 'PORTAL_PERMISSION_SAVED');
        }
        throw new RuntimeException('INVALID_ACTION');
    } catch (Throwable $e) {
        cpAdminRedirect($selectedCompanyId, 'danger', $e->getMessage());
    }
}

$cpSettings = $selectedCompanyId > 0 ? $customerPortalService->getCompanySettings($selectedCompanyId) : [];
$cpLegacyOptions = $selectedCompanyId > 0 ? $customerPortalService->getCompanyLegacyOptions($selectedCompanyId) : ['units'=>[],'rbacs'=>[]];
$cpPortalUsers = $selectedCompanyId > 0 ? $customerPortalService->listCompanyUsers($selectedCompanyId) : [];
$cpPermissionUsers = array_values(array_filter($cpPortalUsers, static function (array $user): bool {
    return in_array(strtoupper((string) ($user['portal_role'] ?? '')), ['ADMIN', 'MANAGER'], true);
}));
$cpPermissionMap = [];
foreach ($cpPortalUsers as $portalUser) {
    $cpPermissionMap[(int)$portalUser['user_id']] = $customerPortalService->getUserPermissions((int)$portalUser['user_id'], $selectedCompanyId);
}

// INTEK global audit browser. This is read-only and remains available even when
// a selected company's feature flag is disabled.
$cpAuditCompanyId = isset($_GET['audit_company_id']) ? max(0, (int) $_GET['audit_company_id']) : $selectedCompanyId;
$cpAuditFilters = [
    'company_id' => $cpAuditCompanyId,
    'section_id' => max(0, (int) ($_GET['audit_section_id'] ?? 0)),
    'user_id' => max(0, (int) ($_GET['audit_user_id'] ?? 0)),
    'role' => (string) ($_GET['audit_role'] ?? ''),
    'action' => (string) ($_GET['audit_action'] ?? ''),
];
$cpAdminAudit = $customerPortalService->listAuditForAdmin($cpAuditFilters, 500);
$cpAuditUsers = $customerPortalService->listPortalUsersForAdminAudit($cpAuditCompanyId);
$cpAuditSections = $customerPortalService->listSectionsForAdminAudit($cpAuditCompanyId);
$cpAuditActions = $customerPortalService->getAuditActionNames($cpAuditCompanyId);

