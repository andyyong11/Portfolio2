<?php
require_once 'MurachDB_Access.php';

class StudentService {
    private $db;

    public function __construct() {
        $this->db = new MurachDB_Access();
    }

    public function getStudent($id) {
        return $this->db->selectOne($id);
    }

    public function getAllStudents() {
        return $this->db->displayRecords('lstf_example');
    }
}
?> 