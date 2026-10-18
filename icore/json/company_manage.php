<?php
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";

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

$langFile = "../lang/{$adminLanguage}.php";
if (is_file($langFile)) {
    require $langFile;
}

define('_lang', $config->getLang($adminLanguage));

function companyLang(string $key, string $fallbackKey = 'invalid_request'): string
{
    if (isset(_lang[$key])) {
        return (string) _lang[$key];
    }
    if (isset(_lang[$fallbackKey])) {
        return (string) _lang[$fallbackKey];
    }
    return $key;
}

function companyRespond(string $status, string $message, int $httpCode = 200, array $data = []): void
{
    http_response_code($httpCode);
    echo json_encode(
        array_merge(['status' => $status, 'message' => $message], $data),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function companyCleanText($value): string
{
    return trim(strip_tags((string) $value));
}

function companyTextLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function companyFlag(string $name): int
{
    return ((string) ($_POST[$name] ?? '0') === '1') ? 1 : 0;
}

function companyExistsByName(mysqli $db, string $name, int $excludeId = 0): bool
{
    if ($excludeId > 0) {
        $stmt = $db->prepare("SELECT id FROM company_profiles WHERE company_name = ? AND id <> ? LIMIT 1");
        $stmt->bind_param('si', $name, $excludeId);
    } else {
        $stmt = $db->prepare("SELECT id FROM company_profiles WHERE company_name = ? LIMIT 1");
        $stmt->bind_param('s', $name);
    }

    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

function companyActivityExists(mysqli $db, int $activityId): bool
{
    if ($activityId <= 0) {
        return false;
    }

    $stmt = $db->prepare("SELECT id FROM activities WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

/**
 * Find every BASE TABLE in the current schema that has an exact company_id column.
 * This deliberately protects both legacy and future additive tables without hardcoding
 * only today's dependency list.
 */
function companyDependencySummary(mysqli $db, int $companyId): array
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
          AND c.COLUMN_NAME = 'company_id'
          AND t.TABLE_TYPE = 'BASE TABLE'
          AND c.TABLE_NAME <> 'company_profiles'
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

        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM `{$table}` WHERE company_id = ?");
        if (!$stmt) {
            continue;
        }

        $stmt->bind_param('i', $companyId);
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
    companyRespond('error', companyLang('invalid_request'), 405);
}

$adminId = (int) ($_SESSION['admin_id'] ?? 0);
if ($adminId <= 0) {
    companyRespond('error', companyLang('company_unauthorized'), 401);
}

$stmt = $db->prepare("SELECT id, status, rbac_id FROM admins WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin || strcasecmp((string) $admin['status'], 'Active') !== 0) {
    companyRespond('error', companyLang('company_unauthorized'), 401);
}

if (!$rbacClass->checkPermissionSubpartByName('company', 'structure', 'systems')) {
    companyRespond('error', companyLang('company_forbidden'), 403);
}

$csrf = (string) ($_POST['csrf'] ?? '');
$sessionCsrf = (string) ($_SESSION['company_csrf'] ?? '');
if ($sessionCsrf === '' || $csrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    companyRespond('error', companyLang('company_invalid_csrf'), 403);
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$operationMap = [
    'add' => 'add_operation',
    'edit' => 'edit_operation',
    'delete' => 'delete_operation',
];

if (!isset($operationMap[$action])) {
    companyRespond('error', companyLang('invalid_request'), 400);
}

if (!$rbacClass->checkPermissionOperationByName($operationMap[$action])) {
    companyRespond('error', companyLang('company_forbidden'), 403);
}

try {
    if ($action === 'add') {
        $name = companyCleanText($_POST['company_name'] ?? '');
        $phone = companyCleanText($_POST['company_phone'] ?? '');
        $activityId = (int) ($_POST['activity_id'] ?? 0);

        if ($name === '') {
            companyRespond('error', companyLang('company_name_required'), 422);
        }
        if (companyTextLength($name) > 255) {
            companyRespond('error', companyLang('company_name_too_long'), 422);
        }
        if (companyTextLength($phone) > 20) {
            companyRespond('error', companyLang('company_phone_too_long'), 422);
        }
        if ($phone !== '' && !preg_match('/^[0-9+()\-\s]+$/u', $phone)) {
            companyRespond('error', companyLang('company_invalid_phone'), 422);
        }
        if (!companyActivityExists($db, $activityId)) {
            companyRespond('error', companyLang('company_activity_required'), 422);
        }
        if (companyExistsByName($db, $name)) {
            companyRespond('error', companyLang('duplicate_data'), 409);
        }

        $companyDevelopment = companyFlag('company_development');
        $deliveryDevelopment = companyFlag('delivery_development');
        $companySupport = companyFlag('company_support');
        $deliverySupport = companyFlag('delivery_support');

        $stmt = $db->prepare(
            "INSERT INTO company_profiles
                (company_name, company_phone, activity_id, company_development, company_support, delivery_development, delivery_support)
             VALUES (?, NULLIF(?, ''), ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'ssiiiii',
            $name,
            $phone,
            $activityId,
            $companyDevelopment,
            $companySupport,
            $deliveryDevelopment,
            $deliverySupport
        );
        $stmt->execute();
        $newId = (int) $stmt->insert_id;
        $stmt->close();

        companyRespond('success', companyLang('insert_success'), 200, ['id' => $newId]);
    }

    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = companyCleanText($_POST['company_name'] ?? '');
        $phone = companyCleanText($_POST['company_phone'] ?? '');
        $activityId = (int) ($_POST['activity_id'] ?? 0);

        if ($id <= 0) {
            companyRespond('error', companyLang('invalid_request'), 422);
        }
        if ($name === '') {
            companyRespond('error', companyLang('company_name_required'), 422);
        }
        if (companyTextLength($name) > 255) {
            companyRespond('error', companyLang('company_name_too_long'), 422);
        }
        if (companyTextLength($phone) > 20) {
            companyRespond('error', companyLang('company_phone_too_long'), 422);
        }
        if ($phone !== '' && !preg_match('/^[0-9+()\-\s]+$/u', $phone)) {
            companyRespond('error', companyLang('company_invalid_phone'), 422);
        }
        if (!companyActivityExists($db, $activityId)) {
            companyRespond('error', companyLang('company_activity_required'), 422);
        }

        $stmt = $db->prepare("SELECT id, activity_id FROM company_profiles WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$current) {
            companyRespond('error', companyLang('company_not_found'), 404);
        }
        if (companyExistsByName($db, $name, $id)) {
            companyRespond('error', companyLang('duplicate_data'), 409);
        }

        $currentActivityId = (int) ($current['activity_id'] ?? 0);
        if ($currentActivityId !== $activityId) {
            $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM rbac WHERE company_id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $roleCount = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
            $stmt->close();

            if ($roleCount > 0) {
                companyRespond('error', companyLang('company_activity_change_blocked'), 409, [
                    'role_count' => $roleCount,
                ]);
            }
        }

        $companyDevelopment = companyFlag('company_development');
        $deliveryDevelopment = companyFlag('delivery_development');
        $companySupport = companyFlag('company_support');
        $deliverySupport = companyFlag('delivery_support');

        $stmt = $db->prepare(
            "UPDATE company_profiles
             SET company_name = ?,
                 company_phone = NULLIF(?, ''),
                 activity_id = ?,
                 company_development = ?,
                 company_support = ?,
                 delivery_development = ?,
                 delivery_support = ?
             WHERE id = ?"
        );
        $stmt->bind_param(
            'ssiiiiii',
            $name,
            $phone,
            $activityId,
            $companyDevelopment,
            $companySupport,
            $deliveryDevelopment,
            $deliverySupport,
            $id
        );
        $stmt->execute();
        $stmt->close();

        companyRespond('success', companyLang('update_success'));
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            companyRespond('error', companyLang('invalid_request'), 422);
        }

        $stmt = $db->prepare("SELECT id FROM company_profiles WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$exists) {
            companyRespond('error', companyLang('company_not_found'), 404);
        }

        $dependency = companyDependencySummary($db, $id);
        if ((int) $dependency['total'] > 0) {
            $message = companyLang('company_delete_in_use');
            if (strpos($message, '%') !== false) {
                $message = sprintf($message, (int) $dependency['total']);
            }

            companyRespond('error', $message, 409, [
                'dependency_count' => (int) $dependency['total'],
                'dependencies' => $dependency['tables'],
            ]);
        }

        $stmt = $db->prepare("DELETE FROM company_profiles WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected < 1) {
            companyRespond('error', companyLang('company_not_found'), 404);
        }

        companyRespond('success', companyLang('delete_success'));
    }
} catch (Throwable $e) {
    companyRespond('error', companyLang('company_unexpected_error'), 500);
}

companyRespond('error', companyLang('invalid_request'), 400);
