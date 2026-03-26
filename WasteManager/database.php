<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host     = getenv('DB_HOST') ?: 'db';
        $this->db_name  = getenv('DB_NAME') ?: 'wastemanager';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: 'secret';
    }

    public function getConnection() {
        $this->conn = null;
        $attempts = 5;

        while ($attempts > 0) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                return $this->conn;
            } catch(PDOException $e) {
                $attempts--;
                if ($attempts === 0) {
                    die("DB Connection failed: " . $e->getMessage());
                }
                sleep(2); // wait 2 seconds and retry
            }
        }
    }
}

function getDB() {
    $database = new Database();
    return $database->getConnection();
}
?>