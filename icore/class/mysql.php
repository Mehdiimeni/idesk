<?php

class Database
{
    private static ?Database $instance = null;
    private Configuration $config;
    private ?mysqli $connection = null;
    private string $host;
    private string $user;
    private string $password;
    private string $database;

    private function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->loadConfig();
        $this->connect();
    }

    public static function getInstance(Configuration $config): Database
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    private function loadConfig(): void
    {
        $allowedHosts = $this->config->getConfig('allowedHosts') ?? [];

        $httpHost = $_SERVER['HTTP_HOST'] ?? '';

        $environment = in_array($httpHost, $allowedHosts, true)
            ? 'localhost'
            : 'production';

        $this->host = $this->config->getDB($environment, 'host');
        $this->user = $this->config->getDB($environment, 'user');
        $this->password = $this->config->getDB($environment, 'password');
        $this->database = $this->config->getDB($environment, 'database');
<<<<<<< HEAD
    }

    private function connect(): void
    {
        mysqli_report(MYSQLI_REPORT_OFF);

        $charset = (stripos(PHP_OS, 'WIN') === 0) ? "utf8" : "utf8mb4";

        $this->connection = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database
        );

        if ($this->connection->connect_error) {
            throw new Exception(
                "Error failed to connect to MySQL: " . $this->connection->connect_error
            );
        }

        if (!$this->connection->set_charset($charset)) {
            throw new Exception(
                "Error failed to set MySQL charset: " . $this->connection->error
            );
        }
    }

    public function getConnection(): mysqli
    {
        if ($this->connection === null) {
            $this->connect();
=======
    
        $charset = (stripos(PHP_OS, 'WIN') === 0) ? "utf8" : "utf8mb4";
    
        $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        $conn->set_charset($charset);
    
        if ($conn->connect_error) {
            die("Error failed to connect to MySQL: " . $conn->connect_error);
        } else {
            return $conn;
        }
    }
    
    
    public function getConnection()
    {
        $charset = (stripos(PHP_OS, 'WIN') === 0) ? "utf8" : "utf8mb4";
    
        $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        $conn->set_charset($charset);
    
        if ($conn->connect_error) {
            die("Error failed to connect to MySQL: " . $conn->connect_error);
        } else {
            return $conn;
>>>>>>> 5591029... some change
        }

        if (!$this->connection->ping()) {
            $this->connect();
        }

        return $this->connection;
    }

    public function closeConnection(): void
    {
        if ($this->connection instanceof mysqli) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize Database singleton.");
    }
    
    
    
}

?>