<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:8080"); // Specify the origin for CORS
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Handle CORS preflight requests
    exit(0);
}

include 'config/db.php'; // Include your database configuration

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (!isset($_GET['user_id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'user_id is required']);
            exit;
        }
        $user_id = intval($_GET['user_id']);
        $query = $conn->prepare("SELECT * FROM savings_goals WHERE user_id = ?");
        $query->bind_param("i", $user_id);
        $query->execute();
        $result = $query->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode($rows);
        break;

    case 'POST':
        if (!isset($input['user_id'], $input['category'], $input['goal']) || empty($input['category'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing or invalid input']);
            exit;
        }
        $user_id = intval($input['user_id']);
        $category = $input['category'];
        $goal = floatval($input['goal']);
        $query = $conn->prepare("INSERT INTO savings_goals (user_id, category, goal) VALUES (?, ?, ?)");
        $query->bind_param("isd", $user_id, $category, $goal);
        $query->execute();
        if ($query->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to insert data']);
        }
        break;

    case 'PUT':
        if (!isset($input['id'], $input['goal']) || !isset($input['goal'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing or invalid input']);
            exit;
        }
        $id = intval($input['id']);
        $goal = floatval($input['goal']);
        $query = $conn->prepare("UPDATE savings_goals SET goal = ? WHERE id = ?");
        $query->bind_param("di", $goal, $id);
        $query->execute();
        if ($query->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update data or no changes made']);
        }
        break;

    case 'DELETE':
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'id is required']);
            exit;
        }
        $id = intval($input['id']);
        $query = $conn->prepare("DELETE FROM savings_goals WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        if ($query->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete data']);
        }
        break;
}

if (isset($conn)) {
    mysqli_close($conn);
}
?>
