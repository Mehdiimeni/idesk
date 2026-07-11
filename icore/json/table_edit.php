<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "../class/mysql.php";
    require_once "../class/config.php";

    $config = Configuration::getInstance();
    $database = Database::getInstance($config);
    $db = $database->getConnection();

    $admin_language = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguage');
    require_once "../lang/{$admin_language}.php";
    define('_lang', $config->getLang($admin_language));

    try {
        $data = $_POST['formEditData'];
        $arrData = json_decode($data, true);

        $table_set = $arrData['table_set'];

        $unique_fields = unserialize(base64_decode($arrData['unique_fields']));
        $unique_fields = is_array($unique_fields) ? $unique_fields : array($unique_fields);

        $id = $arrData['id'];
        function checkUniqueFields($db, $table_set, $unique_fields, $uniqueValues, $id)
        {
            $placeholdersArray = array_fill(0, count($unique_fields), '?');
            $placeholders = implode(' AND ', $placeholdersArray);

            $stmt = $db->prepare("SELECT COUNT(*) as count FROM $table_set WHERE $placeholders AND id != ?");
            $uniqueValues[] = $id;
            $stmt->bind_param(str_repeat('s', count($unique_fields) + 1), ...$uniqueValues);

            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            return $count == 0;
        }

        if (is_array($unique_fields)) {
            foreach ($unique_fields as $field) {
                $uniqueValues[] = $arrData[$field];
            }
        }



        if (!checkUniqueFields($db, $table_set, $unique_fields, $uniqueValues, $id)) {
            echo json_encode(['status' => 'error', 'message' => _lang['duplicate_data']]);
            exit;
        }

        $updateFields = [];
        $updateValues = [];
        $types = "";

        foreach ($arrData as $field => $value) {

            if ($field == 'password' && !empty($value)) {
                $updateFields[] = "$field = ?";
                $updateValues[] = password_hash(htmlspecialchars(strip_tags($value)), PASSWORD_BCRYPT);
                $types .= 's';
            }

            if (in_array($field, ['stracture', 'operation', 'parts']) && !empty($value)) {
                $updateFields[] = "$field = ?";
                $updateValues[] = serialize(htmlspecialchars($value));
                $types .= 's';
            }

            if (
                $field !== 'table_set' && $field !== 'password' && $field !== 'unique_fields' &&
                !in_array($field, ['stracture', 'operation', 'parts'])
            ) {

                $updateFields[] = "$field = ?";
                $updateValues[] = htmlspecialchars(strip_tags($value));

                $types .= stripos($field, 'id') !== false ? 'i' : 's';
            }
        }

        if (empty($updateFields)) {
            echo json_encode(['status' => 'error', 'message' => _lang['no_field_update']]);
            exit;
        }

        $updateFields = implode(', ', $updateFields);

        $stmt = $db->prepare("UPDATE $table_set SET $updateFields WHERE id = ?");
        $updateValues[] = $id;
        $stmt->bind_param($types . 'i', ...$updateValues);

        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => _lang['update_success']]);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}


?>