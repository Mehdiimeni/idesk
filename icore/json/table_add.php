<?php
require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin_language = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguage');
require_once "../lang/{$admin_language}.php";
define('_lang', $config->getLang($admin_language));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => _lang['invalid_request']]);
    exit;
}


try {
    $data = $_POST['formAddData'];
    $arrData = json_decode($data, true);

    $table_set = $arrData['table_set'];

    $unique_fields = (array) unserialize(base64_decode($arrData['unique_fields']));

    $insertFields = [];
    $insertValues = [];
    $types = "";

    foreach ($arrData as $field => $value) {
        if (!in_array($field, ['table_set', 'unique_fields'])) {
            if (!is_array($value)) {
                $insertFields[] = $field;
                $insertValues[] = $field === 'password' ? password_hash(htmlspecialchars(strip_tags($value)),PASSWORD_BCRYPT) : strip_tags($value);
                $types .= stripos($field, 'id') !== false ? 'i' : 's';
            }
        }

        if ($field === 'stracture' && !empty($value)) {
            $insertFields[] = $field;
            $insertValues[] = serialize($value);
            $types .= 's';
        }

        if (in_array($field, ['operation', 'parts']) && !empty($value)) {
            $insertFields[] = $field;
            $insertValues[] = serialize($value);
            $types .= 's';
        }
    }

    // حذف فیلدهای تکراری
    $insertFields = array_unique($insertFields);

    $placeholders = rtrim(str_repeat('?, ', count($insertFields)), ', ');
    $fields = implode(', ', $insertFields);

    $stmt = $db->prepare("INSERT INTO $table_set ($fields) VALUES ($placeholders)");
    $stmt->bind_param($types, ...$insertValues);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => _lang['insert_success']]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
