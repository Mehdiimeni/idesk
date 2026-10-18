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
if (!is_array($allowedLanguages)) {
    $allowedLanguages = [];
}
$adminLanguage = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguageAdmin');
if (!in_array($adminLanguage, $allowedLanguages, true)) {
    $adminLanguage = $config->getConfig('defaultLanguageAdmin');
}
$langFile = "../lang/{$adminLanguage}.php";
if (is_file($langFile)) {
    require $langFile;
}
define('_lang', $config->getLang($adminLanguage));

function accessManageLang(string $key, string $fallbackKey = 'invalid_request'): string
{
    if (isset(_lang[$key])) return (string) _lang[$key];
    if (isset(_lang[$fallbackKey])) return (string) _lang[$fallbackKey];
    return $key;
}

function accessManageRespond(string $status, string $message, int $httpCode = 200, array $data = []): void
{
    http_response_code($httpCode);
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function accessManageCleanText($value): string
{
    return trim(strip_tags((string) $value));
}

function accessManageTextLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function accessManageRoleExistsByName(mysqli $db, string $name, int $excludeId = 0): bool
{
    if ($excludeId > 0) {
        $stmt = $db->prepare("SELECT id FROM rbac WHERE rbac_name = ? AND id <> ? LIMIT 1");
        $stmt->bind_param('si', $name, $excludeId);
    } else {
        $stmt = $db->prepare("SELECT id FROM rbac WHERE rbac_name = ? LIMIT 1");
        $stmt->bind_param('s', $name);
    }
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

function accessManageUnitMeta(mysqli $db, int $unitId): ?array
{
    if ($unitId <= 0) return null;
    $stmt = $db->prepare("\n        SELECT u.id AS unit_id, u.company_id, cp.company_name, cp.activity_id, a.activity_name\n        FROM units u\n        INNER JOIN company_profiles cp ON cp.id = u.company_id\n        LEFT JOIN activities a ON a.id = cp.activity_id\n        WHERE u.id = ?\n        LIMIT 1\n    ");
    $stmt->bind_param('i', $unitId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function accessManageTagExists(mysqli $db, int $tagId): bool
{
    if ($tagId <= 0) return true;
    $stmt = $db->prepare("SELECT id FROM tags WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $tagId);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

function accessManageParentInfo(mysqli $db, int $parentId): ?array
{
    if ($parentId <= 0) return null;
    $stmt = $db->prepare("SELECT id, manager_id, company_id, all_rbac FROM rbac WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $parentId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function accessManageWouldCreateCycle(mysqli $db, int $roleId, int $parentId): bool
{
    if ($roleId <= 0 || $parentId <= 0) return false;
    if ($roleId === $parentId) return true;

    $cursor = $parentId;
    $visited = [];
    for ($i = 0; $i < 100 && $cursor > 0; $i++) {
        if ($cursor === $roleId) return true;
        if (isset($visited[$cursor])) return true;
        $visited[$cursor] = true;

        $stmt = $db->prepare("SELECT manager_id FROM rbac WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $cursor);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$row) return false;
        $cursor = (int) ($row['manager_id'] ?? 0);
    }
    return false;
}

function accessManageDependencySummary(mysqli $db, int $roleId): array
{
    $dependencies = [];
    $total = 0;

    $columns = $db->query("\n        SELECT c.TABLE_NAME, c.COLUMN_NAME\n        FROM information_schema.COLUMNS c\n        INNER JOIN information_schema.TABLES t\n            ON t.TABLE_SCHEMA = c.TABLE_SCHEMA\n           AND t.TABLE_NAME = c.TABLE_NAME\n        WHERE c.TABLE_SCHEMA = DATABASE()\n          AND t.TABLE_TYPE = 'BASE TABLE'\n          AND c.TABLE_NAME <> 'rbac'\n          AND c.COLUMN_NAME LIKE '%rbac_id'\n        ORDER BY c.TABLE_NAME, c.COLUMN_NAME\n    ");

    if ($columns) {
        while ($col = $columns->fetch_assoc()) {
            $table = (string) $col['TABLE_NAME'];
            $column = (string) $col['COLUMN_NAME'];
            if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $column)) continue;

            $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM `{$table}` WHERE `{$column}` = ?");
            if (!$stmt) continue;
            $stmt->bind_param('i', $roleId);
            $stmt->execute();
            $count = (int) (($stmt->get_result()->fetch_assoc()['cnt'] ?? 0));
            $stmt->close();
            if ($count > 0) {
                $dependencies[$table . '.' . $column] = $count;
                $total += $count;
            }
        }
        $columns->free();
    }

    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM rbac WHERE manager_id = ?");
    $stmt->bind_param('i', $roleId);
    $stmt->execute();
    $children = (int) (($stmt->get_result()->fetch_assoc()['cnt'] ?? 0));
    $stmt->close();
    if ($children > 0) {
        $dependencies['rbac.manager_id'] = $children;
        $total += $children;
    }

    return ['total' => $total, 'items' => $dependencies];
}

function accessManageCanRepairMissingUnit(mysqli $db, int $roleId, int $selectedUnitId, int $selectedCompanyId, int $currentCompanyId, int $currentUnitId): bool
{
    if ($currentUnitId > 0 || $selectedUnitId <= 0) return false;
    if ($currentCompanyId > 0 && $currentCompanyId !== $selectedCompanyId) return false;

    $unitIds = [];
    foreach (['admins', 'users'] as $table) {
        $stmt = $db->prepare("SELECT DISTINCT unit_id FROM {$table} WHERE rbac_id = ? AND unit_id IS NOT NULL AND unit_id > 0");
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $unitIds[(int) $row['unit_id']] = true;
        }
        $stmt->close();
    }

    return count($unitIds) === 1 && isset($unitIds[$selectedUnitId]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    accessManageRespond('error', accessManageLang('invalid_request'), 405);
}

$adminId = (int) ($_SESSION['admin_id'] ?? 0);
if ($adminId <= 0) {
    accessManageRespond('error', accessManageLang('access_unauthorized'), 401);
}

$stmt = $db->prepare("SELECT id, status, rbac_id FROM admins WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$admin || strcasecmp((string) $admin['status'], 'Active') !== 0) {
    accessManageRespond('error', accessManageLang('access_unauthorized'), 401);
}

if (!$rbacClass->checkPermissionPartByName('user_access', 'access')) {
    accessManageRespond('error', accessManageLang('access_forbidden'), 403);
}

$csrf = (string) ($_POST['csrf'] ?? '');
$sessionCsrf = (string) ($_SESSION['user_access_csrf'] ?? '');
if ($sessionCsrf === '' || $csrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    accessManageRespond('error', accessManageLang('access_invalid_csrf'), 403);
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$operationMap = [
    'add' => 'add_operation',
    'edit' => 'edit_operation',
    'delete' => 'delete_operation',
];
if (!isset($operationMap[$action])) {
    accessManageRespond('error', accessManageLang('invalid_request'), 400);
}
if (!$rbacClass->checkPermissionOperationByName($operationMap[$action])) {
    accessManageRespond('error', accessManageLang('access_forbidden'), 403);
}

try {
    if ($action === 'add') {
        $name = accessManageCleanText($_POST['rbac_name'] ?? '');
        $description = accessManageCleanText($_POST['rbac_description'] ?? '');
        $unitId = (int) ($_POST['unit_id'] ?? 0);
        $managerId = (int) ($_POST['manager_id'] ?? 0);
        $tagId = (int) ($_POST['tag_id'] ?? 0);

        if ($name === '') accessManageRespond('error', accessManageLang('access_name_required'), 422);
        if (accessManageTextLength($name) > 255) accessManageRespond('error', accessManageLang('access_name_too_long'), 422);
        if (accessManageRoleExistsByName($db, $name)) accessManageRespond('error', accessManageLang('access_duplicate_role'), 409);

        $unit = accessManageUnitMeta($db, $unitId);
        if (!$unit) accessManageRespond('error', accessManageLang('access_unit_invalid'), 422);
        $companyId = (int) ($unit['company_id'] ?? 0);
        $activityId = (int) ($unit['activity_id'] ?? 0);
        if ($companyId <= 0 || $activityId <= 0) accessManageRespond('error', accessManageLang('access_company_activity_missing'), 422);

        if (!accessManageTagExists($db, $tagId)) accessManageRespond('error', accessManageLang('access_tag_invalid'), 422);

        if ($managerId > 0) {
            $parent = accessManageParentInfo($db, $managerId);
            if (!$parent) accessManageRespond('error', accessManageLang('access_parent_invalid'), 422);
            if ((int) ($parent['all_rbac'] ?? 0) !== 1 && (int) ($parent['company_id'] ?? 0) !== $companyId) {
                accessManageRespond('error', accessManageLang('access_parent_company_mismatch'), 422);
            }
        }

        $stmt = $db->prepare("\n            INSERT INTO rbac\n                (rbac_name, rbac_description, manager_id, all_rbac, activity_id, company_id, unit_id, tag_id)\n            VALUES (?, ?, ?, 0, ?, ?, ?, NULLIF(?, 0))\n        ");
        $stmt->bind_param('ssiiiii', $name, $description, $managerId, $activityId, $companyId, $unitId, $tagId);
        $stmt->execute();
        $newId = (int) $stmt->insert_id;
        $stmt->close();

        accessManageRespond('success', accessManageLang('insert_success'), 200, ['id' => $newId]);
    }

    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = accessManageCleanText($_POST['rbac_name'] ?? '');
        $description = accessManageCleanText($_POST['rbac_description'] ?? '');
        $unitId = (int) ($_POST['unit_id'] ?? 0);
        $managerId = (int) ($_POST['manager_id'] ?? 0);
        $tagId = (int) ($_POST['tag_id'] ?? 0);

        if ($id <= 0) accessManageRespond('error', accessManageLang('invalid_request'), 422);
        if ($name === '') accessManageRespond('error', accessManageLang('access_name_required'), 422);
        if (accessManageTextLength($name) > 255) accessManageRespond('error', accessManageLang('access_name_too_long'), 422);

        $stmt = $db->prepare("SELECT * FROM rbac WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$current) accessManageRespond('error', accessManageLang('access_role_not_found'), 404);
        if (accessManageRoleExistsByName($db, $name, $id)) accessManageRespond('error', accessManageLang('access_duplicate_role'), 409);

        if ((int) ($current['all_rbac'] ?? 0) === 1) {
            $stmt = $db->prepare("UPDATE rbac SET rbac_name = ?, rbac_description = ? WHERE id = ?");
            $stmt->bind_param('ssi', $name, $description, $id);
            $stmt->execute();
            $stmt->close();
            accessManageRespond('success', accessManageLang('update_success'));
        }

        $unit = accessManageUnitMeta($db, $unitId);
        if (!$unit) accessManageRespond('error', accessManageLang('access_unit_invalid'), 422);
        $companyId = (int) ($unit['company_id'] ?? 0);
        $activityId = (int) ($unit['activity_id'] ?? 0);
        if ($companyId <= 0 || $activityId <= 0) accessManageRespond('error', accessManageLang('access_company_activity_missing'), 422);
        if (!accessManageTagExists($db, $tagId)) accessManageRespond('error', accessManageLang('access_tag_invalid'), 422);

        $currentUnitId = (int) ($current['unit_id'] ?? 0);
        if ($unitId !== $currentUnitId) {
            $dependencies = accessManageDependencySummary($db, $id);
            $canRepair = accessManageCanRepairMissingUnit(
                $db,
                $id,
                $unitId,
                $companyId,
                (int) ($current['company_id'] ?? 0),
                $currentUnitId
            );
            if ((int) $dependencies['total'] > 0 && !$canRepair) {
                accessManageRespond('error', accessManageLang('access_unit_change_blocked'), 409, [
                    'dependency_count' => (int) $dependencies['total'],
                    'dependencies' => $dependencies['items'],
                ]);
            }
        }

        if ($managerId === $id) accessManageRespond('error', accessManageLang('access_parent_cycle'), 422);
        if ($managerId > 0) {
            $parent = accessManageParentInfo($db, $managerId);
            if (!$parent) accessManageRespond('error', accessManageLang('access_parent_invalid'), 422);
            if ((int) ($parent['all_rbac'] ?? 0) !== 1 && (int) ($parent['company_id'] ?? 0) !== $companyId) {
                accessManageRespond('error', accessManageLang('access_parent_company_mismatch'), 422);
            }
            if (accessManageWouldCreateCycle($db, $id, $managerId)) {
                accessManageRespond('error', accessManageLang('access_parent_cycle'), 422);
            }
        }

        $stmt = $db->prepare("\n            UPDATE rbac\n            SET rbac_name = ?,\n                rbac_description = ?,\n                manager_id = ?,\n                activity_id = ?,\n                company_id = ?,\n                unit_id = ?,\n                tag_id = NULLIF(?, 0)\n            WHERE id = ?\n        ");
        $stmt->bind_param('ssiiiiii', $name, $description, $managerId, $activityId, $companyId, $unitId, $tagId, $id);
        $stmt->execute();
        $stmt->close();

        accessManageRespond('success', accessManageLang('update_success'));
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) accessManageRespond('error', accessManageLang('invalid_request'), 422);

        $stmt = $db->prepare("SELECT id, all_rbac FROM rbac WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$current) accessManageRespond('error', accessManageLang('access_role_not_found'), 404);
        if ((int) ($current['all_rbac'] ?? 0) === 1) {
            accessManageRespond('error', accessManageLang('access_protected_delete_blocked'), 409);
        }

        $dependencies = accessManageDependencySummary($db, $id);
        if ((int) $dependencies['total'] > 0) {
            $message = accessManageLang('access_delete_dependency_message');
            if (strpos($message, '%') !== false) {
                $message = sprintf($message, (int) $dependencies['total']);
            }
            accessManageRespond('error', $message, 409, [
                'dependency_count' => (int) $dependencies['total'],
                'dependencies' => $dependencies['items'],
            ]);
        }

        $stmt = $db->prepare("DELETE FROM rbac WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        if ($affected < 1) accessManageRespond('error', accessManageLang('access_role_not_found'), 404);

        accessManageRespond('success', accessManageLang('delete_success'));
    }
} catch (Throwable $e) {
    error_log('[user_access_manage] ' . $e->getMessage());
    accessManageRespond('error', accessManageLang('access_unexpected_error'), 500);
}

accessManageRespond('error', accessManageLang('invalid_request'), 400);
