<?php
require_once 'DB.php';

class Category {
    private $conn;

    public function __construct() {
        $this->conn = DB::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT * FROM categories";
        $result = $this->conn->query($sql);

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function create($name, $description) {
        $stmt = $this->conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        return $stmt->execute();
    }

    public function update($id, $name, $description) {
        $stmt = $this->conn->prepare("UPDATE categories SET name = ?, description = ? WHERE category_id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
