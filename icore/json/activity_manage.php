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

function activityLang(string $key, string $fallbackKey = 'invalid_request'): string
{
    if (isset(_lang[$key])) {
        return (string) _lang[$key];
    }
    if (isset(_lang[$fallbackKey])) {
        return (string) _lang[$fallbackKey];
    }
    return $key;
}

function activityRespond(string $status, string $message, int $httpCode = 200, array $data = []): void
{
    http_response_code($httpCode);
    echo json_encode(
        array_merge(['status' => $status, 'message' => $message], $data),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    activityRespond('error', activityLang('invalid_request'), 405);
}

$adminId = (int) ($_SESSION['admin_id'] ?? 0);
if ($adminId <= 0) {
    activityRespond('error', activityLang('activity_unauthorized'), 401);
}

$stmt = $db->prepare("SELECT id, status, rbac_id FROM admins WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin || strcasecmp((string) $admin['status'], 'Active') !== 0) {
    activityRespond('error', activityLang('activity_unauthorized'), 401);
}

if (!$rbacClass->checkPermissionSubpartByName('activity', 'structure', 'systems')) {
    activityRespond('error', activityLang('activity_forbidden'), 403);
}

$csrf = (string) ($_POST['csrf'] ?? '');
$sessionCsrf = (string) ($_SESSION['activity_csrf'] ?? '');
if ($sessionCsrf === '' || $csrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    activityRespond('error', activityLang('activity_invalid_csrf'), 403);
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$operationMap = [
    'add' => 'add_operation',
    'edit' => 'edit_operation',
    'delete' => 'delete_operation',
];

if (!isset($operationMap[$action])) {
    activityRespond('error', activityLang('invalid_request'), 400);
}

if (!$rbacClass->checkPermissionOperationByName($operationMap[$action])) {
    activityRespond('error', activityLang('activity_forbidden'), 403);
}

function activityCleanText($value): string
{
    return trim(strip_tags((string) $value));
}

function activityNameLength(string $name): int
{
    return function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);
}

function activityExistsByName(mysqli $db, string $name, int $excludeId = 0): bool
{
    if ($excludeId > 0) {
        $stmt = $db->prepare("SELECT id FROM activities WHERE activity_name = ? AND id <> ? LIMIT 1");
        $stmt->bind_param('si', $name, $excludeId);
    } else {
        $stmt = $db->prepare("SELECT id FROM activities WHERE activity_name = ? LIMIT 1");
        $stmt->bind_param('s', $name);
    }

    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

try {
    if ($action === 'add') {
        $name = activityCleanText($_POST['activity_name'] ?? '');
        $description = activityCleanText($_POST['activity_description'] ?? '');

        if ($name === '') {
            activityRespond('error', activityLang('activity_name_required'), 422);
        }
        if (activityNameLength($name) > 255) {
            activityRespond('error', activityLang('activity_name_too_long'), 422);
        }
        if (activityExistsByName($db, $name)) {
            activityRespond('error', activityLang('duplicate_data'), 409);
        }

        $stmt = $db->prepare("INSERT INTO activities (activity_name, activity_description) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $description);
        $stmt->execute();
        $newId = (int) $stmt->insert_id;
        $stmt->close();

        activityRespond('success', activityLang('insert_success'), 200, ['id' => $newId]);
    }

    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = activityCleanText($_POST['activity_name'] ?? '');
        $description = activityCleanText($_POST['activity_description'] ?? '');

        if ($id <= 0) {
            activityRespond('error', activityLang('invalid_request'), 422);
        }
        if ($name === '') {
            activityRespond('error', activityLang('activity_name_required'), 422);
        }
        if (activityNameLength($name) > 255) {
            activityRespond('error', activityLang('activity_name_too_long'), 422);
        }

        $stmt = $db->prepare("SELECT id FROM activities WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$exists) {
            activityRespond('error', activityLang('activity_not_found'), 404);
        }
        if (activityExistsByName($db, $name, $id)) {
            activityRespond('error', activityLang('duplicate_data'), 409);
        }

        $stmt = $db->prepare("UPDATE activities SET activity_name = ?, activity_description = ? WHERE id = ?");
        $stmt->bind_param('ssi', $name, $description, $id);
        $stmt->execute();
        $stmt->close();

        activityRespond('success', activityLang('update_success'));
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            activityRespond('error', activityLang('invalid_request'), 422);
        }

        $stmt = $db->prepare(
            "SELECT
                (SELECT COUNT(*) FROM company_profiles WHERE activity_id = ?) AS company_count,
                (SELECT COUNT(*) FROM rbac WHERE activity_id = ?) AS role_count"
        );
        $stmt->bind_param('ii', $id, $id);
        $stmt->execute();
        $usage = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $companyCount = (int) ($usage['company_count'] ?? 0);
        $roleCount = (int) ($usage['role_count'] ?? 0);

        if ($companyCount > 0 || $roleCount > 0) {
            $message = activityLang('activity_delete_in_use');
            if (strpos($message, '%') !== false) {
                $message = sprintf($message, $companyCount, $roleCount);
            }
            activityRespond('error', $message, 409, [
                'company_count' => $companyCount,
                'role_count' => $roleCount,
            ]);
        }

        $stmt = $db->prepare("DELETE FROM activities WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected < 1) {
            activityRespond('error', activityLang('activity_not_found'), 404);
        }

        activityRespond('success', activityLang('delete_success'));
    }
} catch (Throwable $e) {
    activityRespond('error', activityLang('activity_unexpected_error'), 500);
}

activityRespond('error', activityLang('invalid_request'), 400);
