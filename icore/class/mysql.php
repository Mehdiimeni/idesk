<?php

class Database
{
    private static $instance;

    private $host;
    private $user;
    private $password;
    private $database;
    private $config;

    /**
     * اتصال فعال دیتابیس
     *
     * @var mysqli|null
     */
    private $connection = null;

    // Dependency Injection
    private function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->initializeConnection();
    }

    // Use getInstance for Singleton pattern
    public static function getInstance(Configuration $config)
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    private function initializeConnection()
    {
        $allowedHosts = $this->config->getConfig('allowedHosts');

        if (!is_array($allowedHosts)) {
            $allowedHosts = [];
        }

        $currentHost = isset($_SERVER['HTTP_HOST'])
            ? $_SERVER['HTTP_HOST']
            : '';

        $environment = in_array($currentHost, $allowedHosts, true)
            ? 'localhost'
            : 'production';

        $this->host = $this->config->getDB($environment, 'host');
        $this->user = $this->config->getDB($environment, 'user');
        $this->password = $this->config->getDB($environment, 'password');
        $this->database = $this->config->getDB($environment, 'database');

        $charset = (stripos(PHP_OS, 'WIN') === 0)
            ? 'utf8'
            : 'utf8mb4';

        $this->connection = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database
        );

        if ($this->connection->connect_error) {
            die(
                'Error failed to connect to MySQL: ' .
                $this->connection->connect_error
            );
        }

        if (!$this->connection->set_charset($charset)) {
            die(
                'Error loading character set ' .
                $charset .
                ': ' .
                $this->connection->error
            );
        }

        return $this->connection;
    }

    public function getConnection()
    {
        /*
         * اگر اتصال ساخته نشده یا قطع شده باشد،
         * دوباره اتصال ایجاد می‌شود.
         */
        if (
            $this->connection === null ||
            !$this->connection->ping()
        ) {
            $this->initializeConnection();
        }

        return $this->connection;
    }
}

?>