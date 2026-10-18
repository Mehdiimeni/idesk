<?php
require_once "../class/config.php";
header('Content-Type: application/json; charset=UTF-8');
$config = Configuration::getInstance();
$lang = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguage');
$file = "../lang/{$lang}.php";
if (is_file($file)) require $file;
define('_lang', $config->getLang($lang));
http_response_code(403);
echo json_encode([
    'success' => false,
    'message' => _lang['view_access_legacy_endpoint_blocked'] ?? 'Forbidden'
], JSON_UNESCAPED_UNICODE);
