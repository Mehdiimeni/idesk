<?php
require_once "../class/mysql.php";
require_once "../class/config.php";

try {
    // تنظیمات و اتصال به دیتابیس
    $config = Configuration::getInstance();
    $database = Database::getInstance($config);
    $db = $database->getConnection();

    // تعیین زبان مدیر
    $admin_language = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguage');
    require_once "../lang/{$admin_language}.php";
    define('_lang', $config->getLang($admin_language));

    $request_id = $_POST['id'] ?? null;
    if (empty($request_id) || !is_numeric($request_id)) {
        throw new Exception("Invalid request ID.");
    }

    // دریافت پاسخ‌ها
    $person_hour_response = $_POST['person_hour_response'] ?? null;
    $delivery_time_response = $_POST['delivery_time_response'] ?? null;

    $response = null;
    if (!empty($person_hour_response)) {
        $response = $person_hour_response;  
    }

    if (!empty($delivery_time_response)) {
        $response = $delivery_time_response;  
    }

 
    $sql = "UPDATE requests SET response = ?, response_view = 1 WHERE id = ?";
    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . htmlspecialchars($db->error));
    }

    $stmt->bind_param('si', $response, $request_id);

    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . htmlspecialchars($stmt->error));
    }

    echo "Update successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
  
    if (isset($db)) {
        $db->close();
    }
}
