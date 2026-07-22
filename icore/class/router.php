<?php
class Router
{
    private $routes = [];
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addRoute($url, $handler)
    {
        $this->routes[$url] = $handler;
    }

    public function handleRequest($url, $base = '.')
    {
        $handler = $this->getHandler($url);

        if ($handler !== null) {
            $this->invokeHandler($handler, $base);
        } else {
            $this->notFound();
        }
    }

    private function getHandler($url)
    {
        foreach ($this->routes as $route => $handler) {
            if ($this->matchRoute($route, $url, $params)) {
                $_GET = array_merge($_GET, $params);
                return $handler;
            }
        }

        return null;
    }

    private function matchRoute($route, $url, &$params)
    {
        $urlParts = explode('/', trim($url, '/'));
        $routeParts = explode('/', trim($route, '/'));

        if (count($urlParts) !== count($routeParts)) {
            return false;
        }

        $params = [];

        foreach ($routeParts as $index => $part) {
            if (strpos($part, '{') !== false && strpos($part, '}') !== false) {
                $paramName = trim($part, '{}');
                $params[$paramName] = $urlParts[$index];
            } elseif ($urlParts[$index] !== $part) {
                return false;
            }
        }

        return true;
    }

    private function invokeHandler($handler, $base)
    {
        $handlerPath = "$base/page/$handler.php";
        
        if (file_exists($handlerPath)) {
            include_once($handlerPath);
        } else {
            $this->notFound();
        }
    }

    private function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}
