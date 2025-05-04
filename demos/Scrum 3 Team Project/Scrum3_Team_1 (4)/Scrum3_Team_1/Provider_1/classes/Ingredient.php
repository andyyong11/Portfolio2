<?php
require_once 'DB.php';

class Ingredient {
    private $conn;

    public function __construct() {
        $this->conn = DB::getInstance()->getConnection();
    }

    public function getAll() {
        $sql = "SELECT i.*, m.name AS item_name 
                FROM ingredients i 
                LEFT JOIN menu_items m ON i.item_id = m.item_id";
        $result = $this->conn->query($sql);

        $ingredients = [];
        while ($row = $result->fetch_assoc()) {
            $ingredients[] = $row;
        }

        return $ingredients;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT i.*, m.name AS item_name 
             FROM ingredients i 
             LEFT JOIN menu_items m ON i.item_id = m.item_id 
             WHERE ingredient_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function create($item_id, $name, $quantity, $unit) {
        $stmt = $this->conn->prepare(
            "INSERT INTO ingredients (item_id, name, quantity, unit) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isds", $item_id, $name, $quantity, $unit);
        return $stmt->execute();
    }

    public function update($id, $item_id, $name, $quantity, $unit) {
        $stmt = $this->conn->prepare(
            "UPDATE ingredients 
             SET item_id = ?, name = ?, quantity = ?, unit = ? 
             WHERE ingredient_id = ?"
        );
        $stmt->bind_param("isdsi", $item_id, $name, $quantity, $unit, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM ingredients WHERE ingredient_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
