<?php
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";

header('Content-Type: application/json; charset=UTF-8');

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$rbacClass = new RBAC($db);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$allowedLanguages = $config->getConfig('allowedLanguage');
if (!is_array($allowedLanguages)) $allowedLanguages = [];
$adminLanguage = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguageAdmin');
if (!in_array($adminLanguage, $allowedLanguages, true)) $adminLanguage = $config->getConfig('defaultLanguageAdmin');
$langFile = "../lang/{$adminLanguage}.php";
if (is_file($langFile)) require $langFile;
define('_lang', $config->getLang($adminLanguage));

function usmLang(string $key, string $fallback = 'invalid_request'): string
{
    if (isset(_lang[$key])) return (string) _lang[$key];
    if (isset(_lang[$fallback])) return (string) _lang[$fallback];
    return $key;
}
function usmRespond(string $status, string $message, int $code = 200, array $data = []): void
{
    http_response_code($code);
    echo json_encode(array_merge(['status'=>$status,'message'=>$message], $data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function usmParseUnitIds($raw): array
{
    $values = is_array($raw) ? $raw : explode(',', (string) $raw);
    $ids = [];
    foreach ($values as $value) {
        $id = (int) trim((string) $value);
        if ($id > 0) $ids[$id] = true;
    }
    $ids = array_keys($ids);
    sort($ids, SORT_NUMERIC);
    return $ids;
}
function usmCurrentAdminCompany(mysqli $db, int $adminId): int
{
    $stmt = $db->prepare("SELECT u.company_id FROM admins a INNER JOIN units u ON u.id = a.unit_id WHERE a.id = ? LIMIT 1");
    if (!$stmt) return 0;
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int) ($row['company_id'] ?? 0);
}
function usmRole(mysqli $db, int $roleId): ?array
{
    $stmt = $db->prepare("SELECT id, rbac_name, all_rbac, company_id, unit_id FROM rbac WHERE id = ? LIMIT 1");
    if (!$stmt) return null;
    $stmt->bind_param('i', $roleId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}
function usmRoleUsage(mysqli $db, string $table, int $roleId): int
{
    if (!in_array($table, ['admins','users'], true)) return 0;
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM {$table} WHERE rbac_id = ?");
    $stmt->bind_param('i', $roleId);
    $stmt->execute();
    $cnt = (int) (($stmt->get_result()->fetch_assoc()['cnt'] ?? 0));
    $stmt->close();
    return $cnt;
}
function usmPermissionRowCount(mysqli $db, int $roleId): int
{
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM permissions_stracture WHERE rbac_id = ?");
    $stmt->bind_param('i', $roleId);
    $stmt->execute();
    $cnt = (int) (($stmt->get_result()->fetch_assoc()['cnt'] ?? 0));
    $stmt->close();
    return $cnt;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') usmRespond('error', usmLang('invalid_request'), 405);

$adminId = (int) ($_SESSION['admin_id'] ?? 0);
if ($adminId <= 0) usmRespond('error', usmLang('structure_access_unauthorized'), 401);
$stmt = $db->prepare("SELECT id, status FROM admins WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$admin || strcasecmp((string)$admin['status'], 'Active') !== 0) usmRespond('error', usmLang('structure_access_unauthorized'), 401);

if (!$rbacClass->checkPermissionPartByName('user_stracture', 'access')) usmRespond('error', usmLang('structure_access_forbidden'), 403);

$csrf = (string) ($_POST['csrf'] ?? '');
$sessionCsrf = (string) ($_SESSION['user_stracture_csrf'] ?? '');
if ($csrf === '' || $sessionCsrf === '' || !hash_equals($sessionCsrf, $csrf)) usmRespond('error', usmLang('structure_access_invalid_csrf'), 403);

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$roleId = (int) ($_POST['rbac_id'] ?? 0);
if ($roleId <= 0) usmRespond('error', usmLang('structure_access_role_invalid'), 422);

$role = usmRole($db, $roleId);
if (!$role) usmRespond('error', usmLang('structure_access_role_invalid'), 404);
if ((int)($role['all_rbac'] ?? 0) === 1) usmRespond('error', usmLang('structure_access_protected_blocked'), 403);

try {
    if ($action === 'save') {
        $existingRows = usmPermissionRowCount($db, $roleId);
        $requiredOperation = $existingRows > 0 ? 'edit_operation' : 'add_operation';
        if (!$rbacClass->checkPermissionOperationByName($requiredOperation)) usmRespond('error', usmLang('structure_access_forbidden'), 403);

        $selectedUnitIds = usmParseUnitIds($_POST['unit_ids'] ?? '');
        $roleCompanyId = (int) ($role['company_id'] ?? 0);
        $homeUnitId = (int) ($role['unit_id'] ?? 0);
        $adminUsage = usmRoleUsage($db, 'admins', $roleId);
        $userUsage = usmRoleUsage($db, 'users', $roleId);
        $currentAdminCompanyId = usmCurrentAdminCompany($db, $adminId);

        $canCrossCompany = false;
        if ($userUsage === 0 && ($adminUsage > 0 || ($currentAdminCompanyId > 0 && $roleCompanyId === $currentAdminCompanyId))) {
            $canCrossCompany = true;
        }

        if (!$canCrossCompany && $roleCompanyId <= 0) usmRespond('error', usmLang('structure_access_role_scope_invalid'), 422);

        $unitCompanyStmt = $db->prepare("SELECT id, company_id FROM units WHERE id = ? LIMIT 1");
        if (!$unitCompanyStmt) throw new RuntimeException('unit lookup failed');
        foreach ($selectedUnitIds as $unitId) {
            $unitCompanyStmt->bind_param('i', $unitId);
            $unitCompanyStmt->execute();
            $unit = $unitCompanyStmt->get_result()->fetch_assoc();
            if (!$unit) {
                $unitCompanyStmt->close();
                usmRespond('error', usmLang('structure_access_unit_invalid'), 422);
            }
            if (!$canCrossCompany && (int)$unit['company_id'] !== $roleCompanyId) {
                $unitCompanyStmt->close();
                usmRespond('error', usmLang('structure_access_cross_company_blocked'), 403);
            }
        }
        $unitCompanyStmt->close();

        // Normal configuration always contains the RBAC home Unit when one exists.
        // Explicit full revoke is a separate clear action protected by delete_operation.
        if ($homeUnitId > 0 && !in_array($homeUnitId, $selectedUnitIds, true)) {
            $selectedUnitIds[] = $homeUnitId;
            sort($selectedUnitIds, SORT_NUMERIC);
        }

        $serialized = serialize(array_map('strval', $selectedUnitIds));
        $db->begin_transaction();
        try {
            if ($existingRows > 0) {
                // Synchronize all legacy duplicate rows instead of deleting them.
                $stmt = $db->prepare("UPDATE permissions_stracture SET stracture = ? WHERE rbac_id = ?");
                $stmt->bind_param('si', $serialized, $roleId);
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $db->prepare("INSERT INTO permissions_stracture (rbac_id, stracture) VALUES (?, ?)");
                $stmt->bind_param('is', $roleId, $serialized);
                $stmt->execute();
                $stmt->close();
            }
            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
        usmRespond('success', usmLang('structure_access_save_success'));
    }

    if ($action === 'clear') {
        if (!$rbacClass->checkPermissionOperationByName('delete_operation')) usmRespond('error', usmLang('structure_access_forbidden'), 403);
        $existingRows = usmPermissionRowCount($db, $roleId);
        if ($existingRows <= 0) usmRespond('success', usmLang('structure_access_clear_success'));
        $empty = serialize([]);
        // No physical delete: revoke structure scope while keeping legacy record/history intact.
        $stmt = $db->prepare("UPDATE permissions_stracture SET stracture = ? WHERE rbac_id = ?");
        $stmt->bind_param('si', $empty, $roleId);
        $stmt->execute();
        $stmt->close();
        usmRespond('success', usmLang('structure_access_clear_success'));
    }

    usmRespond('error', usmLang('invalid_request'), 400);
} catch (Throwable $e) {
    usmRespond('error', usmLang('structure_access_unexpected_error'), 500);
}
