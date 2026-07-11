<?php

class Configuration
{
    private static $instance;
    private $db;
    private $config;
    private $route;

    protected $defaultFileName = "avatar-1.jpg";
    protected $defaultFilePath = "./itheme/panel/images/users/";

    private function __construct()
    {
        // بارگذاری فایل‌های پیکربندی
        $this->loadConfigurationFiles();
    }

    private function loadConfigurationFiles()
    {
        // بارگذاری پیکربندی پایگاه داده
        $dbFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'db.php';
        if (!file_exists($dbFile)) {
            throw new Exception("Database configuration file not found.");
        }
        $this->db = include $dbFile;

        // بارگذاری سایر فایل‌های پیکربندی
        $configFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'config.php';
        if (!file_exists($configFile)) {
            throw new Exception("Configuration file not found.");
        }
        $this->config = include $configFile;

        $routeFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'route.php';
        if (!file_exists($routeFile)) {
            throw new Exception("Route configuration file not found.");
        }
        $this->route = include $routeFile;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getDB(string $key, string $key2)
    {
        return $this->db[$key][$key2] ?? null;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getLang($lang)
    {
        $allowedLanguages = $this->config['allowedLanguage'] ?? [];
        if (in_array($lang, $allowedLanguages)) {
            $filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $lang . '.php';
            if (file_exists($filePath)) {
                return include $filePath;
            } else {
                throw new Exception("Language file not found.");
            }
        }

        return null;
    }

    public function getConfig(string $key, string $key2 = '')
    {
        return $key2 === '' ? ($this->config[$key] ?? null) : ($this->config[$key][$key2] ?? null);
    }

    public function getNowLanguage($part)
    {
        if ($part == 'a') {
            return $_COOKIE['admin_language'] ?? $this->getConfig('defaultLanguageAdmin');
        } elseif ($part == 'u') {
            return $_COOKIE['user_language'] ?? $this->getConfig('defaultLanguage');
        }

        return '';
    }

    public function getDefaultFileName() {
        return $this->defaultFileName;
    }

    public function getDefaultFilePath() {
        return $this->defaultFilePath;
    }
}

?>
