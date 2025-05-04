<?php
header('Content-Type: application/json');
require_once 'class_lib/StudentService.php';

$id = $_GET['id'] ?? null;
$service = new StudentService();

if ($id) {
    $student = $service->getStudent($id);
    if ($student === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Student not found']);
    } else {
        echo json_encode($student);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID parameter is required']);
}
?> 