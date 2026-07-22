<?php

use iweb\model\UserModel;
///controller/user/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = Configuration::getInstance();
$userLanguage = isset($_COOKIE['user_language']) ? $_COOKIE['user_language'] : $config->getConfig('defaultLanguage');
define('_lang', $config->getLang($userLanguage));
$userLanguageDir = in_array($userLanguage, ['fa', 'ar']) ? 'rtl' : 'ltr';
setcookie('userLanguageDir', $userLanguageDir, time() + 7 * 24 * 60 * 60, '/');

$allLanguages = $config->getConfig('allLanguage');


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);

if ($userModel->loggedIn()) {
    echo "<script>window.location.replace('./index');</script>";
}


$loginMessage = '';
if (!empty($_POST["login"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
    $userModel->email = $_POST["email"];
    $userModel->password = $_POST["password"];
    if ($userModel->login()) {
        echo "<script>window.location.replace('./index');</script>";
    } else {
        $loginMessage = _lang['invalid_login'];
    }
} else if (!empty($_POST["login"])) {
    $loginMessage = _lang['field_null'];
}
