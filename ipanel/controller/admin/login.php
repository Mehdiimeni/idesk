<?php

use ipanel\model\AdminModel;
///controller/admin/login.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = Configuration::getInstance();
$adminLanguage = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguageAdmin');
define('_lang', $config->getLang($adminLanguage));
$adminLanguageDir = in_array($adminLanguage, ['fa', 'ar']) ? 'rtl' : 'ltr';
setcookie('adminLanguageDir', $adminLanguageDir, time() + 7 * 24 * 60 * 60, '/');


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$adminModel = new AdminModel($db);

if ($adminModel->loggedIn()) {
    echo "<script>window.location.replace('./index');</script>";
    exit;
}

$allLanguages = $config->getConfig('allLanguage');

$loginMessage = '';
if (!empty($_POST["login"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {

    $adminModel->email = $_POST["email"];
    $adminModel->password = $_POST["password"];

    if ($adminModel->login()) {
        echo "<script>window.location.replace('./index');</script>";
        exit;

    } else {

        $loginMessage = _lang['invalid_login'];
    }

} elseif (!empty($_POST["login"])) {

    $loginMessage = _lang['field_null'];

}