<?php
<<<<<<< HEAD

namespace ICore;
=======
namespace icore\class;
>>>>>>> 5591029... some change

class SessionTools
{
    private const SESSION_LIFETIME = 18000;

<<<<<<< HEAD
    private function __construct()
    {
    }

    public static function init(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::checkLifetime();
            return;
        }

        if (!headers_sent()) {
            session_set_cookie_params([
                'lifetime' => self::SESSION_LIFETIME,
                'path' => '/',
                'secure' => self::isHttps(),
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        if (!ob_get_level() && extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        }

        session_start();

        self::checkLifetime();

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function set(string $name, mixed $value): void
=======
    public static function init()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'lifetime' => self::SESSION_LIFETIME,
                'httponly' => true,
                'secure' => isset($_SERVER['HTTPS']),
                'samesite' => 'Strict'
            ]);

            if (!ob_get_level()) {
                ob_start("ob_gzhandler");
                

            }
            session_start();

            if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > self::SESSION_LIFETIME)) {
                self::destroy();
                session_start(); 
            }
            $_SESSION['LAST_ACTIVITY'] = time();
        }
    }

    public static function set(string $name, $value)
>>>>>>> 5591029... some change
    {
        self::init();
        $_SESSION[$name] = $value;
    }

<<<<<<< HEAD
    public static function get(string $name, bool $sanitize = false): mixed
    {
        self::init();

        $value = $_SESSION[$name] ?? null;

        if (!$sanitize) {
            return $value;
        }

        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        return $value;
    }

    public static function has(string $name): bool
    {
        self::init();
        return isset($_SESSION[$name]);
    }

    public static function destroyByName(string $name): void
=======
    public static function get(string $name, $sanitize = false)
    {
        self::init();
        $value = $_SESSION[$name] ?? null;
        return $sanitize ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
    }

    public static function destroyByName(string $name)
>>>>>>> 5591029... some change
    {
        self::init();
        unset($_SESSION[$name]);
    }

<<<<<<< HEAD
    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                $params['secure'] ?? false,
                $params['httponly'] ?? true
            );
        }

        session_unset();
        session_destroy();
    }

    public static function regenerate(): void
    {
        self::init();
        session_regenerate_id(true);
    }

    public static function generateCsrfToken(): string
    {
        self::init();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool
    {
        self::init();

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    private static function checkLifetime(): void
    {
        if (
            isset($_SESSION['LAST_ACTIVITY']) &&
            time() - $_SESSION['LAST_ACTIVITY'] > self::SESSION_LIFETIME
        ) {
            self::destroy();
            session_start();
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function isHttps(): bool
    {
        return (
            !empty($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] !== 'off'
        );
    }
}
=======
    public static function destroy()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }

    public static function generateCsrfToken()
    {
        self::init();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(string $token)
    {
        self::init();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
>>>>>>> 5591029... some change
