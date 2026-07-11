<?php
///controller/global/page_icon.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = Configuration::getInstance();
$userLanguage = $_COOKIE['user_language'] ?? $config->getConfig('defaultLanguage');
define('_lang', $config->getLang($userLanguage));
$userLanguageDir = in_array($userLanguage, ['fa', 'ar']) ? 'rtl' : 'ltr';
$cookieExpiration = time() + (7 * 24 * 60 * 60); 
setcookie('userLanguageDir', $userLanguageDir, $cookieExpiration, '/');

?>