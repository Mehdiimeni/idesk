<?php

use ipanel\model\AdminModel;
///controller/admin/logout.php
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$adminModel = new AdminModel($db);
$adminModel->admin_log('logout');

if (session_status() == PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

if (!headers_sent()) {
    header("Location: ./login");
} else {
    echo "<script>window.location.href='./login';</script>";
}

exit;

