<?php
header('Content-Type: application/json');
require_once 'class_lib/StudentService.php';

$service = new StudentService();
$students = $service->getAllStudents();

$result = [];
while ($row = $students->fetch_assoc()) {
    $result[] = $row;
}

echo json_encode($result);
?> 