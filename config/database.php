<?php
/**
 * MAISON ÉLAN - Database Connection (PDO)
 * Prepared Statements, UTF-8, Singleton Pattern
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private bool $connected = false;
    private ?string $errorMessage = null;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            $this->connected = true;
        } catch (PDOException $e) {
            $this->connected = false;
            $this->errorMessage = $e->getMessage();
            // Log error silently
            error_log("MAISON ÉLAN DB Error: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO {
        return $this->connection;
    }

    public function isConnected(): bool {
        return $this->connected;
    }

    public function getErrorMessage(): ?string {
        return $this->errorMessage;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}

/**
 * Global helper function to get PDO database connection
 */
function getDB(): ?PDO {
    return Database::getInstance()->getConnection();
}
