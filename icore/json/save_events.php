<?php

require_once "../class/mysql.php";
require_once "../class/config.php";

session_start();

// Get database connection
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$response = array('status' => 'error', 'message' => 'An unknown error occurred.');

// Get data from POST request
$id = isset($_POST['id']) && $_POST['id'] !== '' ? $db->real_escape_string($_POST['id']) : null;
$title = isset($_POST['title']) && $_POST['title'] !== '' ? $db->real_escape_string($_POST['title']) : '';
$category = isset($_POST['category']) && $_POST['category'] !== '' ? $db->real_escape_string($_POST['category']) : '';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $db->real_escape_string($_POST['start_date']) : null;
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $db->real_escape_string($_POST['end_date']) : null;

$admin_id = $_SESSION['admin_id'];


// Validate input
if (empty($title) || empty($category) || empty($start_date) || empty($end_date)) {
    $response = array('status' => 'error', 'message' => 'All fields are required.');
    echo json_encode($response);
    exit;
}

// Check if we're adding a new event or updating an existing one
if ($id) {
    // Update existing event
    $query = "UPDATE calendar SET title = ?, category = ?, start_date = ?, end_date = ?,admin_id = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssssii", $title, $category, $start_date, $end_date, $id, $admin_id);
        if ($stmt->execute()) {
            $response = array('status' => 'success', 'message' => 'Event updated successfully.');
        } else {
            $response = array('status' => 'error', 'message' => 'Error updating event: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        $response = array('status' => 'error', 'message' => 'Error preparing statement: ' . $db->error);
    }
} else {
    // Add new event
    $query = "INSERT INTO calendar (title, category, start_date, end_date, admin_id) VALUES (?, ?, ?, ?,?)";
    $stmt = $db->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssssi", $title, $category, $start_date, $end_date, $admin_id);
        if ($stmt->execute()) {
            $response = array('status' => 'success', 'message' => 'Event added successfully.');
        } else {
            $response = array('status' => 'error', 'message' => 'Error adding event: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        $response = array('status' => 'error', 'message' => 'Error preparing statement: ' . $db->error);
    }
}

$db->close();
echo json_encode($response);
?>