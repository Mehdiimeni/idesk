<?php
///controller/global/page_icon.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = Configuration::getInstance();
$adminLanguage = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguageAdmin');

define('_lang', $config->getLang($adminLanguage));

$adminLanguageDir = in_array($adminLanguage, ['fa', 'ar']) ? 'rtl' : 'ltr';

$cookieExpiration = time() + (7 * 24 * 60 * 60); 
setcookie('adminLanguageDir', $adminLanguageDir, $cookieExpiration, '/');

?>