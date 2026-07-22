<?php
namespace icore\class;

class SessionTools
{
    private const SESSION_LIFETIME = 18000;

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
    {
        self::init();
        $_SESSION[$name] = $value;
    }

    public static function get(string $name, $sanitize = false)
    {
        self::init();
        $value = $_SESSION[$name] ?? null;
        return $sanitize ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
    }

    public static function destroyByName(string $name)
    {
        self::init();
        unset($_SESSION[$name]);
    }

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
