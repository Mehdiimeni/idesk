<?php
// chat_box.php
require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin_language = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguage');
require_once "../lang/{$admin_language}.php";
define('_lang', $config->getLang($admin_language));

if (isset($_POST['receiver_id']) && isset($_POST['sender_id'])) {
    $receiver_id = intval($_POST['receiver_id']); 
    $sender_id = intval($_POST['sender_id']); 
    $sender_type = $_POST['sender_type']; 
    $receiver_type = $_POST['receiver_type']; 

    // انتخاب پیام‌ها مربوط به member_id
    $sql = "SELECT * FROM chat_box WHERE ( receiver_id = ? AND sender_id = ? AND sender_type = ? AND receiver_type = ? ) OR ( receiver_id = ? AND sender_id = ? AND sender_type = ? AND receiver_type = ? ) ORDER BY id ASC"; 
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iissiiss", $receiver_id, $sender_id, $sender_type, $receiver_type,$sender_id,$receiver_id,$receiver_type,$sender_type);
    $stmt->execute();
    $result = $stmt->get_result();

    // بررسی و نمایش پیام‌ها
    if ($result->num_rows > 0) {
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $senderName = htmlspecialchars($row['sender_name']); // نام فرستنده
            $messageContent = htmlspecialchars($row['message_content']); // محتوای پیام
            $creation_date = date('H:i', strtotime($row['creation_date'])); // فرمت زمان

            // ساختن آرایه برای هر پیام
            $messages[] = [
                'sender_id' => $row['sender_id'],
                'sender_name' => $senderName,
                'message_content' => $messageContent,
                'creation_date' => $creation_date
            ];
        }
        echo json_encode($messages); // ارسال پیام‌ها به عنوان JSON
    } else {
        echo json_encode([]); // در صورت عدم وجود پیام
    }

    // بستن اتصال
    $stmt->close();
    $db->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid member ID.']); // در صورت عدم وجود member_id
}

?>
