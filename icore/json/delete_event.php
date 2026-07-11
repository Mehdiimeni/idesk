<?php
require_once "../class/mysql.php";
require_once "../class/config.php";

session_start();

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    $stmt = $db->prepare("DELETE FROM calendar WHERE id = ? AND admin_id = ?");
    $stmt->bind_param("ii", $id, $admin_id);

    if ($stmt->execute()) {
        echo "Event deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    echo "Request method not POST.";
}
?>
