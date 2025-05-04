<?php
require_once 'DB.php';

class MenuItem {
    private $conn;

    public function __construct() {
        $this->conn = DB::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT m.*, c.name AS category_name 
                FROM menu_items m 
                LEFT JOIN categories c ON m.category_id = c.category_id";
        $result = $this->conn->query($sql);

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        return $items;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT m.*, c.name AS category_name 
             FROM menu_items m 
             LEFT JOIN categories c ON m.category_id = c.category_id 
             WHERE m.item_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function create($name, $price, $category_id, $description, $available) {
        $stmt = $this->conn->prepare(
            "INSERT INTO menu_items (name, price, category_id, description, available) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sdisi", $name, $price, $category_id, $description, $available);
        return $stmt->execute();
    }

    public function update($id, $name, $price, $category_id, $description, $available) {
        $stmt = $this->conn->prepare(
            "UPDATE menu_items 
             SET name = ?, price = ?, category_id = ?, description = ?, available = ? 
             WHERE item_id = ?"
        );
        $stmt->bind_param("sdisii", $name, $price, $category_id, $description, $available, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM menu_items WHERE item_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
