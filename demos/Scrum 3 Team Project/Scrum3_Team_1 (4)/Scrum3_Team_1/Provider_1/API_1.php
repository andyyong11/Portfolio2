<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'classes/Category.php';
require_once 'classes/MenuItem.php';
require_once 'classes/Ingredient.php';

// Helper function
function jsonResponse($success, $data = null, $message = "") {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

// Capture GET or POST actions
$action = $_REQUEST['action'] ?? '';
$type = $_REQUEST['type'] ?? '';

if (empty($action) || empty($type)) {
    jsonResponse(false, null, "Missing required parameters");
}

try {
    // Route to correct model
    switch ($type) {
        case 'category':
            $model = new Category();
            break;
        case 'menu_item':
            $model = new MenuItem();
            break;
        case 'ingredient':
            $model = new Ingredient();
            break;
        default:
            jsonResponse(false, null, "Invalid type: " . $type);
    }

    // Handle actions
    switch ($action) {
        case 'get_all':
            $data = $model->getAll();
            jsonResponse(true, $data);
            break;

        case 'get':
            $id = $_REQUEST['id'] ?? null;
            if (!$id) {
                jsonResponse(false, null, "Missing ID");
            }
            $data = $model->getById($id);
            jsonResponse(true, $data);
            break;

        case 'create':
            $result = false;
            switch ($type) {
                case 'category':
                    if (!isset($_POST['name']) || !isset($_POST['description'])) {
                        jsonResponse(false, null, "Missing required fields for category");
                    }
                    $result = $model->create($_POST['name'], $_POST['description']);
                    break;
                case 'menu_item':
                    if (!isset($_POST['name']) || !isset($_POST['price']) || !isset($_POST['category_id'])) {
                        jsonResponse(false, null, "Missing required fields for menu item");
                    }
                    $result = $model->create(
                        $_POST['name'],
                        $_POST['price'],
                        $_POST['category_id'],
                        $_POST['description'] ?? '',
                        $_POST['available'] ?? 1
                    );
                    break;
                case 'ingredient':
                    if (!isset($_POST['item_id']) || !isset($_POST['name']) || !isset($_POST['quantity'])) {
                        jsonResponse(false, null, "Missing required fields for ingredient");
                    }
                    $result = $model->create(
                        $_POST['item_id'],
                        $_POST['name'],
                        $_POST['quantity'],
                        $_POST['unit'] ?? ''
                    );
                    break;
            }
            jsonResponse($result, null, $result ? "Created successfully" : "Failed to create");
            break;

        case 'update':
            $id = $_POST['id'] ?? null;
            if (!$id) {
                jsonResponse(false, null, "Missing ID for update");
            }
            
            $result = false;
            switch ($type) {
                case 'category':
                    if (!isset($_POST['name']) || !isset($_POST['description'])) {
                        jsonResponse(false, null, "Missing required fields for category update");
                    }
                    $result = $model->update($id, $_POST['name'], $_POST['description']);
                    break;
                case 'menu_item':
                    if (!isset($_POST['name']) || !isset($_POST['price']) || !isset($_POST['category_id'])) {
                        jsonResponse(false, null, "Missing required fields for menu item update");
                    }
                    $result = $model->update(
                        $id,
                        $_POST['name'],
                        $_POST['price'],
                        $_POST['category_id'],
                        $_POST['description'] ?? '',
                        $_POST['available'] ?? 1
                    );
                    break;
                case 'ingredient':
                    if (!isset($_POST['item_id']) || !isset($_POST['name']) || !isset($_POST['quantity'])) {
                        jsonResponse(false, null, "Missing required fields for ingredient update");
                    }
                    $result = $model->update(
                        $id,
                        $_POST['item_id'],
                        $_POST['name'],
                        $_POST['quantity'],
                        $_POST['unit'] ?? ''
                    );
                    break;
            }
            jsonResponse($result, null, $result ? "Updated successfully" : "Failed to update");
            break;

        case 'delete':
            $id = $_POST['id'] ?? null;
            if (!$id) {
                jsonResponse(false, null, "Missing ID for delete");
            }
            $result = $model->delete($id);
            jsonResponse($result, null, $result ? "Deleted successfully" : "Failed to delete");
            break;

        default:
            jsonResponse(false, null, "Invalid action: " . $action);
    }
} catch (Exception $e) {
    jsonResponse(false, null, "Error: " . $e->getMessage());
}
?>
