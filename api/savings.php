<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Return only the headers and not the content
    // Only allow CORS if we're doing a GET - this should match the requested method
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && 
        $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET') {
        header("Access-Control-Allow-Origin: http://localhost:8080");
    }
    exit(0);
}

include 'config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        $user_id = $_GET['user_id'];
        $query = "SELECT * FROM savings_goals WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        echo json_encode($rows);
        break;
    case 'POST':
        $user_id = $input['user_id'];
        $category = $input['category'];
        $goal = $input['goal'];
        $query = "INSERT INTO savings_goals (user_id, category, goal) VALUES ('$user_id', '$category', '$goal')";
        mysqli_query($conn, $query);
        echo json_encode(['success' => true]);
        break;
    case 'PUT':
        $id = $input['id'];
        $goal = $input['goal'];
        $query = "UPDATE savings_goals SET goal = '$goal' WHERE id = $id";
        mysqli_query($conn, $query);
        echo json_encode(['success' => true]);
        break;
    case 'DELETE':
        $id = $input['id'];
        $query = "DELETE FROM savings_goals WHERE id = $id";
        mysqli_query($conn, $query);
        echo json_encode(['success' => true]);
        break;
}

if (isset($conn)) {
    mysqli_close($conn);
}
?>
