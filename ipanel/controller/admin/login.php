<?php
<<<<<<< HEAD
=======

use ipanel\model\AdminModel;
///controller/admin/login.php
>>>>>>> 5591029... some change

use ipanel\model\AdminModel;
use ICore\SessionTools;

// controller/admin/login.php

SessionTools::init();

$config = Configuration::getInstance();
<<<<<<< HEAD

$adminLanguage = $_COOKIE['admin_language']
    ?? $config->getConfig('defaultLanguageAdmin');

define('_lang', $config->getLang($adminLanguage));

$adminLanguageDir = in_array($adminLanguage, ['fa', 'ar'], true) ? 'rtl' : 'ltr';

setcookie(
    'adminLanguageDir',
    $adminLanguageDir,
    time() + 7 * 24 * 60 * 60,
    '/',
    '',
    SessionTools::isHttps(),
    true
);

=======
$adminLanguage = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguageAdmin');
define('_lang', $config->getLang($adminLanguage));
$adminLanguageDir = in_array($adminLanguage, ['fa', 'ar']) ? 'rtl' : 'ltr';
setcookie('adminLanguageDir', $adminLanguageDir, time() + 7 * 24 * 60 * 60, '/');


$config = Configuration::getInstance();
>>>>>>> 5591029... some change
$database = Database::getInstance($config);
$db = $database->getConnection();

$adminModel = new AdminModel($db);
<<<<<<< HEAD

/*
|--------------------------------------------------------------------------
| Remember Login
|--------------------------------------------------------------------------
*/

if (
    !$adminModel->loggedIn() &&
    !empty($_COOKIE['remember_admin_token'])
) {
    $adminModel->loginByRememberToken($_COOKIE['remember_admin_token']);
}

if ($adminModel->loggedIn()) {
    header('Location: ./index');
=======

if ($adminModel->loggedIn()) {
    echo "<script>window.location.replace('./index');</script>";
>>>>>>> 5591029... some change
    exit;
}

$allLanguages = $config->getConfig('allLanguage');

$loginMessage = '';
<<<<<<< HEAD

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberLogin = !empty($_POST['remember_login']);

    if ($email === '' || $password === '') {

        $loginMessage = _lang['field_null'];

    } else {

        $adminModel->email = $email;
        $adminModel->password = $password;

        if ($adminModel->login()) {

            SessionTools::regenerate();

            if ($rememberLogin) {

                $rememberToken = $adminModel->createRememberToken();

                setcookie(
                    'remember_admin_token',
                    $rememberToken,
                    time() + (30 * 24 * 60 * 60),
                    '/',
                    '',
                    SessionTools::isHttps(),
                    true
                );

            } else {

                setcookie(
                    'remember_admin_token',
                    '',
                    time() - 3600,
                    '/',
                    '',
                    SessionTools::isHttps(),
                    true
                );
            }

            header('Location: ./index');
            exit;
        }

        $loginMessage = _lang['invalid_login'];
    }
=======
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

>>>>>>> 5591029... some change
}