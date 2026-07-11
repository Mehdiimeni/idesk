<?php

$router = Router::getInstance();
$config = Configuration::getInstance();

$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$config = Configuration::getInstance();
$allowedHosts = $config->getConfig('allowedHosts');
$targetIpanelPath = $config->getConfig('targetIpanelPath');

$routes = $config->getRoute();


if (in_array($_SERVER['HTTP_HOST'], $allowedHosts) && strpos($requestUrl, $targetIpanelPath) !== false) {
    $prefix = $config->getConfig('prefixOnline');
    $requestUrl = str_replace($targetIpanelPath, "/", $requestUrl);
} else {
    $prefix = $config->getConfig('prefixLocal');
}

foreach ($routes as $route) {
    $fullPath = $prefix . $route['path'];
    $router->addRoute($fullPath, $route['file']);
}

$router->handleRequest($requestUrl);


