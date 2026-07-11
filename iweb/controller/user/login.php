<?php

use iweb\model\UserModel;
<<<<<<< HEAD
use ICore\SessionTools;

SessionTools::init();

$config = Configuration::getInstance();

$userLanguage = $_COOKIE['user_language']
    ?? $config->getConfig('defaultLanguage');

define('_lang', $config->getLang($userLanguage));
=======
///controller/user/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = Configuration::getInstance();
$userLanguage = isset($_COOKIE['user_language']) ? $_COOKIE['user_language'] : $config->getConfig('defaultLanguage');
define('_lang', $config->getLang($userLanguage));
$userLanguageDir = in_array($userLanguage, ['fa', 'ar']) ? 'rtl' : 'ltr';
setcookie('userLanguageDir', $userLanguageDir, time() + 7 * 24 * 60 * 60, '/');
>>>>>>> 5591029... some change

$userLanguageDir = in_array($userLanguage, ['fa', 'ar'], true) ? 'rtl' : 'ltr';

setcookie(
    'userLanguageDir',
    $userLanguageDir,
    time() + 7 * 24 * 60 * 60,
    '/',
    '',
    SessionTools::isHttps(),
    true
);

<<<<<<< HEAD
$allLanguages = $config->getConfig('allLanguage');

=======
$config = Configuration::getInstance();
>>>>>>> 5591029... some change
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);

<<<<<<< HEAD
if (!$userModel->loggedIn() && !empty($_COOKIE['remember_user_token'])) {
    $userModel->loginByRememberToken($_COOKIE['remember_user_token']);
=======
if ($userModel->loggedIn()) {
    echo "<script>window.location.replace('./index');</script>";
>>>>>>> 5591029... some change
}

if ($userModel->loggedIn()) {
    header('Location: ./index');
    exit;
}

$loginMessage = '';
<<<<<<< HEAD

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberLogin = !empty($_POST['remember_login']);

    if ($email === '' || $password === '') {
        $loginMessage = _lang['field_null'];
=======
if (!empty($_POST["login"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
    $userModel->email = $_POST["email"];
    $userModel->password = $_POST["password"];
    if ($userModel->login()) {
        echo "<script>window.location.replace('./index');</script>";
>>>>>>> 5591029... some change
    } else {
        $userModel->email = $email;
        $userModel->password = $password;

        if ($userModel->login()) {
            SessionTools::regenerate();

            if ($rememberLogin) {
                $rememberToken = $userModel->createRememberToken();

                setcookie(
                    'remember_user_token',
                    $rememberToken,
                    time() + 30 * 24 * 60 * 60,
                    '/',
                    '',
                    SessionTools::isHttps(),
                    true
                );
            } else {
                setcookie('remember_user_token', '', time() - 3600, '/');
            }

            header('Location: ./index');
            exit;
        }

        $loginMessage = _lang['invalid_login'];
    }
<<<<<<< HEAD
}
=======
} else if (!empty($_POST["login"])) {
    $loginMessage = _lang['field_null'];
}
>>>>>>> 5591029... some change
