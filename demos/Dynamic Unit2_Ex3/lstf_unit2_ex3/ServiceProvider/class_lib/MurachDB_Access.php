<?php
class MurachDB_Access {
    private static $conn;
    private static $hostName = "localhost";
    private static $databaseName = "students";
    private static $userName = "root";
    private static $password = "";

    // Connect to MySQL
    public function connectTo() {
        self::$conn = new mysqli(self::$hostName, self::$userName, self::$password, self::$databaseName);
        if (self::$conn->connect_error) {
            return "Connect Error to " . self::$hostName;
        }
        return "Connection successful to hostName = " . self::$hostName . ", databaseName = " . self::$databaseName;
    }

    // Show databases
    public function showDatabases() {
        self::connectTo();
        $query = "SHOW DATABASES";
        $result = self::$conn->query($query);
        return $result;
    }

    // Show tables in the selected database
    public function showTables() {
        self::connectTo();
        $query = "SHOW TABLES FROM " . self::$databaseName;
        $result = self::$conn->query($query);
        return $result;
    }

    // Fetch records from a given table
    public function displayRecords($table) {
        self::connectTo();
        $query = "SELECT * FROM " . $table;
        $result = self::$conn->query($query);
        return $result;
    }
    public function selectOne($id) {
        self::connectTo(); // ensure connection

        $stmt = self::$conn->prepare("SELECT * FROM lstf_example WHERE id = ?");
        if (!$stmt) {
            return "Error: " . self::$conn->error;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }
    
}
?>
