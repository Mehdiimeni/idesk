<?php
// بارگذاری تنظیمات و کلاس‌های ضروری
require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin_language = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguage');
require_once "../lang/{$admin_language}.php";
define('_lang', $config->getLang($admin_language));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message_content']) && !empty($_POST['receiver_id']) && !empty($_POST['sender_id']) && !empty($_POST['receiver_type']) && !empty($_POST['sender_type'])) {

    $messageContent = trim($_POST['message_content']); 
    $receiverId = (int)$_POST['receiver_id']; 
    $senderId = (int)$_POST['sender_id'];
    $receiverType = $_POST['receiver_type'];
    $senderType = $_POST['sender_type'];

    // دریافت نام فرستنده
    $senderNameSql = "SELECT name FROM company_member_view WHERE member_id = ? AND member_type = ?";
    $stmtSender = $db->prepare($senderNameSql);
    $stmtSender->bind_param('is', $senderId, $senderType);
    $stmtSender->execute();
    $stmtSender->bind_result($senderName);
    $stmtSender->fetch();
    $stmtSender->close();

    // دریافت نام گیرنده
    $receiverNameSql = "SELECT name FROM company_member_view WHERE member_id = ? AND member_type = ?";
    $stmtReceiver = $db->prepare($receiverNameSql);
    $stmtReceiver->bind_param('is', $receiverId, $receiverType);
    $stmtReceiver->execute();
    $stmtReceiver->bind_result($receiverName);
    $stmtReceiver->fetch();
    $stmtReceiver->close();

    // حالا می‌توانیم نام‌ها را در INSERT قرار دهیم
    $sql = "INSERT INTO chat_box (message_content, sender_id, sender_name, sender_type, receiver_id, receiver_name, receiver_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param('sississ', $messageContent, $senderId, $senderName, $senderType, $receiverId, $receiverName, $receiverType);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
    }

    $db->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request or missing data']);
}

?>
