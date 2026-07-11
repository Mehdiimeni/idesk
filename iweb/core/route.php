<?php


$router = Router::getInstance();
$config = Configuration::getInstance();

$routes = $config->getRoute();

foreach ($routes as $route) {
    $router->addRoute($route['path'], $route['file']);
}

$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


$allowedHosts = $config->getConfig('allowedHosts');
$allowWebUrl = $config->getConfig('allowWebUrl');
$clearWebUrl = $config->getConfig('clearWebUrl');


if (in_array($_SERVER['HTTP_HOST'], $allowedHosts) && strpos($requestUrl, $allowWebUrl) !== false) {
    $requestUrl = str_replace($clearWebUrl, "/", $requestUrl);
}


$router->handleRequest($requestUrl, "./iweb");
