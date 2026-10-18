<?php
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$rbacClass = new RBAC($db);

$allowedLanguages = $config->getConfig('allowedLanguage');
if (!is_array($allowedLanguages)) {
    $allowedLanguages = [];
}
$adminLanguage = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguageAdmin');
if (!in_array($adminLanguage, $allowedLanguages, true)) {
    $adminLanguage = $config->getConfig('defaultLanguageAdmin');
}
require_once "../lang/{$adminLanguage}.php";
define('_lang', $config->getLang($adminLanguage));

function internalAdminsJson(bool $success, string $message, array $extra = [], int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode(array_merge([
        'status' => $success ? 'success' : 'error',
        'message' => $message,
    ], $extra), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function internalAdminsRoleKind(?string $rbacName): string
{
    $name = trim((string) $rbacName);
    if ($name === '') {
        return 'UNASSIGNED';
    }
    if (mb_stripos($name, 'کارشناس') !== false || stripos($name, 'expert') !== false) {
        return 'EXPERT';
    }
    return 'MANAGER';
}

function internalAdminsLoadUnit(mysqli $db, int $unitId): ?array
{
    $stmt = $db->prepare("SELECT id, company_id, unit_name, unit_type FROM units WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $unitId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function internalAdminsLoadRbac(mysqli $db, int $rbacId): ?array
{
    $stmt = $db->prepare("SELECT id, company_id, unit_id, rbac_name, all_rbac, manager_id FROM rbac WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $rbacId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function internalAdminsLoadTarget(mysqli $db, int $adminId): ?array
{
    $stmt = $db->prepare("
        SELECT
            a.id,
            a.name,
            a.email,
            a.mobile,
            a.status,
            a.role,
            a.unit_id,
            a.rbac_id,
            un.company_id,
            un.unit_name,
            r.rbac_name,
            r.all_rbac,
            r.manager_id
        FROM admins a
        INNER JOIN units un ON un.id = a.unit_id
        LEFT JOIN rbac r ON r.id = a.rbac_id
        WHERE a.id = ?
        LIMIT 1
    ");
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function internalAdminsDuplicateExists(mysqli $db, string $email, string $mobile, int $excludeId = 0): bool
{
    if ($mobile === '') {
        $stmt = $db->prepare("SELECT id FROM admins WHERE id <> ? AND LOWER(email) = LOWER(?) LIMIT 1");
        $stmt->bind_param('is', $excludeId, $email);
    } else {
        $stmt = $db->prepare("SELECT id FROM admins WHERE id <> ? AND (LOWER(email) = LOWER(?) OR mobile = ?) LIMIT 1");
        $stmt->bind_param('iss', $excludeId, $email, $mobile);
    }
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $exists;
}

function internalAdminsCountActiveExperts(mysqli $db, int $companyId, int $unitId): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*) AS cnt
        FROM admins a
        INNER JOIN units un ON un.id = a.unit_id
        INNER JOIN rbac r ON r.id = a.rbac_id
        WHERE un.company_id = ?
          AND a.unit_id = ?
          AND a.status = 'Active'
          AND (r.rbac_name LIKE '%کارشناس%' OR LOWER(r.rbac_name) LIKE '%expert%')
    ");
    $stmt->bind_param('ii', $companyId, $unitId);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    $stmt->close();
    return $count;
}

function internalAdminsCountOtherActiveManagers(mysqli $db, int $companyId, int $unitId, int $excludeAdminId): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*) AS cnt
        FROM admins a
        INNER JOIN units un ON un.id = a.unit_id
        LEFT JOIN rbac r ON r.id = a.rbac_id
        WHERE un.company_id = ?
          AND a.unit_id = ?
          AND a.status = 'Active'
          AND a.id <> ?
          AND (r.id IS NULL OR (r.rbac_name NOT LIKE '%کارشناس%' AND LOWER(r.rbac_name) NOT LIKE '%expert%'))
    ");
    $stmt->bind_param('iii', $companyId, $unitId, $excludeAdminId);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    $stmt->close();
    return $count;
}

function internalAdminsCountActiveManagers(mysqli $db, int $companyId, int $unitId): int
{
    return internalAdminsCountOtherActiveManagers($db, $companyId, $unitId, 0);
}

function internalAdminsCountOtherAllRbacAdmins(mysqli $db, int $companyId, int $excludeAdminId): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*) AS cnt
        FROM admins a
        INNER JOIN units un ON un.id = a.unit_id
        INNER JOIN rbac r ON r.id = a.rbac_id
        WHERE un.company_id = ?
          AND a.status = 'Active'
          AND r.all_rbac = 1
          AND a.id <> ?
    ");
    $stmt->bind_param('ii', $companyId, $excludeAdminId);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    $stmt->close();
    return $count;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    internalAdminsJson(false, _lang['invalid_request'], [], 405);
}

if (empty($_SESSION['admin_id']) || empty($_SESSION['rbac_id']) || empty($_SESSION['company_id'])) {
    internalAdminsJson(false, _lang['internal_admins_unauthorized'], [], 401);
}

if (!$rbacClass->checkPermissionSubpartByName('admins', 'members', 'systems')) {
    internalAdminsJson(false, _lang['internal_admins_forbidden'], [], 403);
}

$csrf = (string) ($_POST['csrf_token'] ?? '');
$sessionCsrf = (string) ($_SESSION['admins_management_csrf'] ?? '');
if ($csrf === '' || $sessionCsrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    internalAdminsJson(false, _lang['internal_admins_invalid_csrf'], [], 419);
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$operationByAction = [
    'add' => 'add_operation',
    'edit' => 'edit_operation',
    'activate' => 'active_operation',
    'inactivate' => 'inactive_operation',
];

if (!isset($operationByAction[$action])) {
    internalAdminsJson(false, _lang['invalid_request'], [], 400);
}

if (!$rbacClass->checkPermissionOperationByName($operationByAction[$action])) {
    internalAdminsJson(false, _lang['internal_admins_forbidden'], [], 403);
}

$companyId = (int) $_SESSION['company_id'];
$actorAdminId = (int) $_SESSION['admin_id'];

if ($action === 'add' || $action === 'edit') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $mobile = trim((string) ($_POST['mobile'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $unitId = (int) ($_POST['unit_id'] ?? 0);
    $rbacId = (int) ($_POST['rbac_id'] ?? 0);

    if ($name === '' || mb_strlen($name) > 200) {
        internalAdminsJson(false, _lang['internal_admins_name_invalid'], [], 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 200) {
        internalAdminsJson(false, _lang['internal_admins_email_invalid'], [], 422);
    }
    if ($mobile !== '' && mb_strlen($mobile) > 25) {
        internalAdminsJson(false, _lang['internal_admins_mobile_invalid'], [], 422);
    }

    $unit = internalAdminsLoadUnit($db, $unitId);
    if (!$unit || (int) $unit['company_id'] !== $companyId) {
        internalAdminsJson(false, _lang['internal_admins_unit_invalid'], [], 422);
    }

    $rbac = internalAdminsLoadRbac($db, $rbacId);
    if (!$rbac || (int) $rbac['company_id'] !== $companyId || (int) $rbac['unit_id'] !== $unitId) {
        internalAdminsJson(false, _lang['internal_admins_rbac_invalid'], [], 422);
    }

    $newOrgRole = internalAdminsRoleKind($rbac['rbac_name'] ?? null);
}

if ($action === 'add') {
    if (mb_strlen($password) < 8) {
        internalAdminsJson(false, _lang['internal_admins_password_required'], [], 422);
    }
    if (internalAdminsDuplicateExists($db, $email, $mobile, 0)) {
        internalAdminsJson(false, _lang['internal_admins_duplicate'], [], 409);
    }
    if ($newOrgRole === 'EXPERT' && internalAdminsCountActiveManagers($db, $companyId, $unitId) <= 0) {
        internalAdminsJson(false, _lang['internal_admins_expert_requires_manager'], [], 409);
    }

    try {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $legacyRole = 'all';
        $status = 'Active';
        $mobileValue = $mobile === '' ? null : $mobile;
        $stmt = $db->prepare("INSERT INTO admins (email, mobile, password, name, role, status, unit_id, rbac_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssii', $email, $mobileValue, $passwordHash, $name, $legacyRole, $status, $unitId, $rbacId);
        $stmt->execute();
        $newAdminId = (int) $stmt->insert_id;
        $stmt->close();

        internalAdminsJson(true, _lang['internal_admins_add_success'], [
            'admin_id' => $newAdminId,
            'org_role' => $newOrgRole,
        ]);
    } catch (Throwable $e) {
        internalAdminsJson(false, _lang['internal_admins_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

$targetAdminId = (int) ($_POST['admin_id'] ?? 0);
$target = internalAdminsLoadTarget($db, $targetAdminId);
if (!$target || (int) $target['company_id'] !== $companyId) {
    internalAdminsJson(false, _lang['internal_admins_not_found'], [], 404);
}

$oldOrgRole = internalAdminsRoleKind($target['rbac_name'] ?? null);
$oldUnitId = (int) $target['unit_id'];
$isTargetActive = (($target['status'] ?? '') === 'Active');

if ($action === 'edit') {
    if (internalAdminsDuplicateExists($db, $email, $mobile, $targetAdminId)) {
        internalAdminsJson(false, _lang['internal_admins_duplicate'], [], 409);
    }

    if ($targetAdminId === $actorAdminId && ($unitId !== $oldUnitId || $rbacId !== (int) $target['rbac_id'])) {
        internalAdminsJson(false, _lang['internal_admins_self_scope_change_blocked'], [], 409);
    }

    if ($isTargetActive && $oldOrgRole === 'MANAGER' && ($unitId !== $oldUnitId || $newOrgRole !== 'MANAGER')) {
        $activeExperts = internalAdminsCountActiveExperts($db, $companyId, $oldUnitId);
        $otherManagers = internalAdminsCountOtherActiveManagers($db, $companyId, $oldUnitId, $targetAdminId);
        if ($activeExperts > 0 && $otherManagers <= 0) {
            internalAdminsJson(false, _lang['internal_admins_last_manager_change_blocked'], [], 409);
        }
    }

    if ($isTargetActive && $newOrgRole === 'EXPERT' && internalAdminsCountActiveManagers($db, $companyId, $unitId) <= 0) {
        internalAdminsJson(false, _lang['internal_admins_expert_requires_manager'], [], 409);
    }

    if ((int) ($target['all_rbac'] ?? 0) === 1 && (int) ($rbac['all_rbac'] ?? 0) !== 1) {
        if (internalAdminsCountOtherAllRbacAdmins($db, $companyId, $targetAdminId) <= 0) {
            internalAdminsJson(false, _lang['internal_admins_last_super_scope_blocked'], [], 409);
        }
    }

    try {
        $mobileValue = $mobile === '' ? null : $mobile;
        if ($password !== '') {
            if (mb_strlen($password) < 8) {
                internalAdminsJson(false, _lang['internal_admins_password_too_short'], [], 422);
            }
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE admins SET name = ?, email = ?, mobile = ?, password = ?, unit_id = ?, rbac_id = ? WHERE id = ?");
            $stmt->bind_param('ssssiii', $name, $email, $mobileValue, $passwordHash, $unitId, $rbacId, $targetAdminId);
        } else {
            $stmt = $db->prepare("UPDATE admins SET name = ?, email = ?, mobile = ?, unit_id = ?, rbac_id = ? WHERE id = ?");
            $stmt->bind_param('sssiii', $name, $email, $mobileValue, $unitId, $rbacId, $targetAdminId);
        }
        $stmt->execute();
        $stmt->close();

        internalAdminsJson(true, _lang['internal_admins_edit_success'], [
            'org_role' => $newOrgRole,
        ]);
    } catch (Throwable $e) {
        internalAdminsJson(false, _lang['internal_admins_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

if ($action === 'inactivate') {
    if ($targetAdminId === $actorAdminId) {
        internalAdminsJson(false, _lang['internal_admins_self_inactivate_blocked'], [], 409);
    }

    if (($target['status'] ?? '') !== 'Active') {
        internalAdminsJson(true, _lang['internal_admins_inactivate_success']);
    }

    if ($oldOrgRole === 'MANAGER') {
        $activeExperts = internalAdminsCountActiveExperts($db, $companyId, $oldUnitId);
        $otherManagers = internalAdminsCountOtherActiveManagers($db, $companyId, $oldUnitId, $targetAdminId);
        if ($activeExperts > 0 && $otherManagers <= 0) {
            internalAdminsJson(false, _lang['internal_admins_last_manager_inactivate_blocked'], [], 409);
        }
    }

    if ((int) ($target['all_rbac'] ?? 0) === 1 && internalAdminsCountOtherAllRbacAdmins($db, $companyId, $targetAdminId) <= 0) {
        internalAdminsJson(false, _lang['internal_admins_last_super_inactivate_blocked'], [], 409);
    }

    try {
        $stmt = $db->prepare("UPDATE admins SET status = 'Inactive' WHERE id = ?");
        $stmt->bind_param('i', $targetAdminId);
        $stmt->execute();
        $stmt->close();
        internalAdminsJson(true, _lang['internal_admins_inactivate_success']);
    } catch (Throwable $e) {
        internalAdminsJson(false, _lang['internal_admins_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

if ($action === 'activate') {
    if (($target['status'] ?? '') === 'Active') {
        internalAdminsJson(true, _lang['internal_admins_activate_success']);
    }

    if ($oldOrgRole === 'EXPERT' && internalAdminsCountActiveManagers($db, $companyId, $oldUnitId) <= 0) {
        internalAdminsJson(false, _lang['internal_admins_expert_requires_manager'], [], 409);
    }

    try {
        $stmt = $db->prepare("UPDATE admins SET status = 'Active' WHERE id = ?");
        $stmt->bind_param('i', $targetAdminId);
        $stmt->execute();
        $stmt->close();
        internalAdminsJson(true, _lang['internal_admins_activate_success']);
    } catch (Throwable $e) {
        internalAdminsJson(false, _lang['internal_admins_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

internalAdminsJson(false, _lang['invalid_request'], [], 400);
