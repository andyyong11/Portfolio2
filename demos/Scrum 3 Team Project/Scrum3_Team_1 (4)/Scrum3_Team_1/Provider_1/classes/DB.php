<?php
class DB {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'menu_manager';
    private static $instance = null;
    public $conn;

    private function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            // Set charset to UTF-8
            if (!$this->conn->set_charset("utf8")) {
                throw new Exception("Error setting charset: " . $this->conn->error);
            }

            // Enable error reporting for debugging
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
        } catch (Exception $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection() {
        if ($this->conn instanceof mysqli && !$this->conn->connect_error) {
            return $this->conn;
        }
        
        // Try to reconnect if connection is lost
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            if ($this->conn->connect_error) {
                throw new Exception("Reconnection failed: " . $this->conn->connect_error);
            }
            return $this->conn;
        } catch (Exception $e) {
            error_log("Database Reconnection Error: " . $e->getMessage());
            throw new Exception("Database connection lost. Please try again later.");
        }
    }
}
?>
