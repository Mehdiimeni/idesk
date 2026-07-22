<?php

use iweb\model\UserModel;
///controller/user/logout.php
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
$userModel->user_log('logout');

session_unset();
session_destroy();

if (!headers_sent()) {
    header("Location: ./login");
    exit;
} else {
    echo "<script>window.location.href='./login';</script>";
    exit;
}

