<?php

class Router
{
    private array $routes = [];

    private static ?Router $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): Router
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addRoute(string $url, string $handler): void
    {
        $url = '/' . trim($url, '/');

        $this->routes[$url] = $handler;
    }

    public function handleRequest(string $url, string $base = '.'): void
    {
<<<<<<< HEAD
        $url = parse_url($url, PHP_URL_PATH) ?? '/';
        $url = '/' . trim($url, '/');

        if ($url === '/') {
            $url = '/index';
        }

=======
>>>>>>> 5591029... some change
        $handler = $this->getHandler($url);

        if ($handler === null) {
            $this->notFound();
            return;
        }

        $this->invokeHandler($handler, $base);
    }

<<<<<<< HEAD
    private function getHandler(string $url): ?string
=======
    private function getHandler($url)
>>>>>>> 5591029... some change
    {
        foreach ($this->routes as $route => $handler) {
            $params = [];

            if ($this->matchRoute($route, $url, $params)) {
                $_GET = array_merge($_GET, $params);
                return $handler;
            }
        }

        return null;
    }

    private function matchRoute(string $route, string $url, array &$params): bool
    {
        $routeParts = explode('/', trim($route, '/'));
        $urlParts = explode('/', trim($url, '/'));

        if (count($routeParts) !== count($urlParts)) {
            return false;
        }

        $params = [];

        foreach ($routeParts as $index => $part) {
            if ($this->isParameter($part)) {
                $paramName = trim($part, '{}');
                $params[$paramName] = urldecode($urlParts[$index]);
                continue;
            }

            if ($urlParts[$index] !== $part) {
                return false;
            }
        }

        return true;
    }

    private function isParameter(string $part): bool
    {
<<<<<<< HEAD
        return str_starts_with($part, '{') && str_ends_with($part, '}');
=======
        $handlerPath = "$base/page/$handler.php";
        
        if (file_exists($handlerPath)) {
            include_once($handlerPath);
        } else {
            $this->notFound();
        }
>>>>>>> 5591029... some change
    }

    private function invokeHandler(string $handler, string $base): void
    {
        $handler = trim($handler, '/');
        $base = rtrim($base, '/');

        $handlerPath = $base . '/page/' . $handler . '.php';

        if (!is_file($handlerPath)) {
            $this->notFound();
            return;
        }

        include_once $handlerPath;
    }
<<<<<<< HEAD

    private function notFound(): void
    {
        if (!headers_sent()) {
            http_response_code(404);
        }

        echo '404 Not Found';
        exit;
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception('Cannot unserialize Router singleton.');
    }
}
=======
}
>>>>>>> 5591029... some change
