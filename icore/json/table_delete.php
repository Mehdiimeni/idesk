<?php

require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin_language = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguage');
require_once "../lang/{$admin_language}.php";
define('_lang', $config->getLang($admin_language));

if (isset($_POST['id']) && isset($_POST['table'])) {
    try {
        $itemId = $_POST['id'];
        $tableName = $_POST['table'];

        // activities, company_profiles, units, types, users, admins, rbac permissions_stracture and permissions_operation are managed by protected page-specific endpoints.
        if (in_array(strtolower(trim((string) $tableName)), ['activities', 'company_profiles', 'units', 'types', 'users', 'admins', 'rbac', 'permissions_stracture', 'permissions_operation'], true)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => _lang['invalid_request']]);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM $tableName WHERE id = ?");
        $itemId = htmlspecialchars(strip_tags($itemId));

        $stmt->bind_param("i", $itemId);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => _lang['delete_success']]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => _lang['invalid_request']]);
}

?>