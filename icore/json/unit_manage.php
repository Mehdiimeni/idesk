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

function unitLang(string $key, string $fallbackKey = 'invalid_request'): string
{
    if (isset(_lang[$key])) {
        return (string) _lang[$key];
    }
    if (isset(_lang[$fallbackKey])) {
        return (string) _lang[$fallbackKey];
    }
    return $key;
}

function unitRespond(string $status, string $message, int $httpCode = 200, array $data = []): void
{
    http_response_code($httpCode);
    echo json_encode(
        array_merge(['status' => $status, 'message' => $message], $data),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function unitCleanText($value): string
{
    return trim(strip_tags((string) $value));
}

function unitTextLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function unitCompanyExists(mysqli $db, int $companyId): bool
{
    if ($companyId <= 0) {
        return false;
    }
    $stmt = $db->prepare("SELECT id FROM company_profiles WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $companyId);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

function unitExistsByNameAndCompany(mysqli $db, string $name, int $companyId, int $excludeId = 0): bool
{
    if ($excludeId > 0) {
        $stmt = $db->prepare("SELECT id FROM units WHERE unit_name = ? AND company_id = ? AND id <> ? LIMIT 1");
        $stmt->bind_param('sii', $name, $companyId, $excludeId);
    } else {
        $stmt = $db->prepare("SELECT id FROM units WHERE unit_name = ? AND company_id = ? LIMIT 1");
        $stmt->bind_param('si', $name, $companyId);
    }
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

/**
 * Protect any current/future BASE TABLE carrying a unit_id column.
 * This avoids hardcoding only users/admins/rbac/daily_reports and keeps delete safe
 * if an additive module later starts referencing units.
 */
function unitDependencySummary(mysqli $db, int $unitId): array
{
    $dependencies = [];
    $total = 0;

    $sql = "
        SELECT c.TABLE_NAME
        FROM information_schema.COLUMNS c
        INNER JOIN information_schema.TABLES t
            ON t.TABLE_SCHEMA = c.TABLE_SCHEMA
           AND t.TABLE_NAME = c.TABLE_NAME
        WHERE c.TABLE_SCHEMA = DATABASE()
          AND c.COLUMN_NAME = 'unit_id'
          AND t.TABLE_TYPE = 'BASE TABLE'
          AND c.TABLE_NAME <> 'units'
        ORDER BY c.TABLE_NAME
    ";

    $result = $db->query($sql);
    if (!$result) {
        return ['total' => 0, 'tables' => []];
    }

    while ($row = $result->fetch_assoc()) {
        $table = (string) $row['TABLE_NAME'];
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            continue;
        }

        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM `{$table}` WHERE unit_id = ?");
        if (!$stmt) {
            continue;
        }
        $stmt->bind_param('i', $unitId);
        $stmt->execute();
        $countRow = $stmt->get_result()->fetch_assoc();
        $count = (int) ($countRow['cnt'] ?? 0);
        $stmt->close();

        if ($count > 0) {
            $dependencies[$table] = $count;
            $total += $count;
        }
    }

    $result->free();
    return ['total' => $total, 'tables' => $dependencies];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unitRespond('error', unitLang('invalid_request'), 405);
}

$adminId = (int) ($_SESSION['admin_id'] ?? 0);
if ($adminId <= 0) {
    unitRespond('error', unitLang('unit_unauthorized'), 401);
}

$stmt = $db->prepare("SELECT id, status, rbac_id FROM admins WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin || strcasecmp((string) $admin['status'], 'Active') !== 0) {
    unitRespond('error', unitLang('unit_unauthorized'), 401);
}

if (!$rbacClass->checkPermissionSubpartByName('unit', 'structure', 'systems')) {
    unitRespond('error', unitLang('unit_forbidden'), 403);
}

$csrf = (string) ($_POST['csrf'] ?? '');
$sessionCsrf = (string) ($_SESSION['unit_csrf'] ?? '');
if ($sessionCsrf === '' || $csrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    unitRespond('error', unitLang('unit_invalid_csrf'), 403);
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$operationMap = [
    'add' => 'add_operation',
    'edit' => 'edit_operation',
    'delete' => 'delete_operation',
];

if (!isset($operationMap[$action])) {
    unitRespond('error', unitLang('invalid_request'), 400);
}

if (!$rbacClass->checkPermissionOperationByName($operationMap[$action])) {
    unitRespond('error', unitLang('unit_forbidden'), 403);
}

try {
    if ($action === 'add') {
        $name = unitCleanText($_POST['unit_name'] ?? '');
        $type = unitCleanText($_POST['unit_type'] ?? '');
        $companyId = (int) ($_POST['company_id'] ?? 0);

        if ($name === '') {
            unitRespond('error', unitLang('unit_name_required'), 422);
        }
        if (unitTextLength($name) > 255) {
            unitRespond('error', unitLang('unit_name_too_long'), 422);
        }
        if (unitTextLength($type) > 255) {
            unitRespond('error', unitLang('unit_type_too_long'), 422);
        }
        if (!unitCompanyExists($db, $companyId)) {
            unitRespond('error', unitLang('unit_company_required'), 422);
        }
        if (unitExistsByNameAndCompany($db, $name, $companyId)) {
            unitRespond('error', unitLang('unit_duplicate_company'), 409);
        }

        $stmt = $db->prepare("INSERT INTO units (unit_name, unit_type, company_id) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $name, $type, $companyId);
        $stmt->execute();
        $stmt->close();
        unitRespond('success', unitLang('insert_success'));
    }

    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = unitCleanText($_POST['unit_name'] ?? '');
        $type = unitCleanText($_POST['unit_type'] ?? '');
        $companyId = (int) ($_POST['company_id'] ?? 0);

        if ($id <= 0) {
            unitRespond('error', unitLang('invalid_request'), 422);
        }
        if ($name === '') {
            unitRespond('error', unitLang('unit_name_required'), 422);
        }
        if (unitTextLength($name) > 255) {
            unitRespond('error', unitLang('unit_name_too_long'), 422);
        }
        if (unitTextLength($type) > 255) {
            unitRespond('error', unitLang('unit_type_too_long'), 422);
        }
        if (!unitCompanyExists($db, $companyId)) {
            unitRespond('error', unitLang('unit_company_required'), 422);
        }

        $stmt = $db->prepare("SELECT id, company_id FROM units WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$current) {
            unitRespond('error', unitLang('unit_not_found'), 404);
        }

        $currentCompanyId = (int) ($current['company_id'] ?? 0);
        if ($companyId !== $currentCompanyId) {
            $dependency = unitDependencySummary($db, $id);
            if ((int) $dependency['total'] > 0) {
                unitRespond('error', unitLang('unit_company_change_blocked'), 409, [
                    'dependency_count' => (int) $dependency['total'],
                    'dependencies' => $dependency['tables'],
                ]);
            }
        }

        if (unitExistsByNameAndCompany($db, $name, $companyId, $id)) {
            unitRespond('error', unitLang('unit_duplicate_company'), 409);
        }

        $stmt = $db->prepare("UPDATE units SET unit_name = ?, unit_type = ?, company_id = ? WHERE id = ?");
        $stmt->bind_param('ssii', $name, $type, $companyId, $id);
        $stmt->execute();
        $stmt->close();
        unitRespond('success', unitLang('update_success'));
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            unitRespond('error', unitLang('invalid_request'), 422);
        }

        $stmt = $db->prepare("SELECT id FROM units WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        if (!$exists) {
            unitRespond('error', unitLang('unit_not_found'), 404);
        }

        $dependency = unitDependencySummary($db, $id);
        if ((int) $dependency['total'] > 0) {
            $message = unitLang('unit_delete_in_use');
            if (strpos($message, '%') !== false) {
                $message = sprintf($message, (int) $dependency['total']);
            }
            unitRespond('error', $message, 409, [
                'dependency_count' => (int) $dependency['total'],
                'dependencies' => $dependency['tables'],
            ]);
        }

        $stmt = $db->prepare("DELETE FROM units WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        unitRespond('success', unitLang('delete_success'));
    }
} catch (Throwable $e) {
    error_log('[unit_manage] ' . $e->getMessage());
    unitRespond('error', unitLang('unit_unexpected_error'), 500);
}

unitRespond('error', unitLang('invalid_request'), 400);
