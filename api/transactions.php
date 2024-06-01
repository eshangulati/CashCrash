<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Return only the headers and not the content
    // Only allow CORS if we're doing a GET - this should match the requested method
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && 
        $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET') {
        header("Access-Control-Allow-Origin: http://localhost:8080");
    }
    exit(0);
}

// Include database configuration
require_once 'config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];
$input = json_decode(file_get_contents('php://input'), true);

// Create SQL based on method
switch ($method) {
    case 'GET':
        if (isset($_GET['user_id'])) {
            $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
            $sql = "SELECT * FROM transactions WHERE user_id='$user_id'";
            $result = mysqli_query($conn, $sql);
            $transactions = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $transactions[] = $row;
            }
            echo json_encode($transactions);
        } else {
            echo json_encode([]);
        }
        break;

    case 'POST':
        if (isset($input['user_id']) && isset($input['date']) && isset($input['category']) && isset($input['merchant']) && isset($input['amount'])) {
            $user_id = mysqli_real_escape_string($conn, $input['user_id']);
            $date = mysqli_real_escape_string($conn, $input['date']);
            $category = mysqli_real_escape_string($conn, $input['category']);
            $merchant = mysqli_real_escape_string($conn, $input['merchant']);
            $amount = mysqli_real_escape_string($conn, $input['amount']);

            $sql = "INSERT INTO transactions (user_id, date, category, merchant, amount) VALUES ('$user_id', '$date', '$category', '$merchant', '$amount')";
            if (mysqli_query($conn, $sql)){
                $updateSql = "UPDATE budgets SET amountSpent = amountSpent + $amount WHERE user_id='$user_id' AND category = '$category'";
                if (mysqli_query($conn, $updateSql)){
                    echo json_encode(['success' => true]);
                } else{
                    echo json_encode(['success' => false, 'message' => 'Failed to update budget']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert transaction']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Incomplete input']);
        }
        break;

    case 'PUT':
        if (isset($input['id']) && isset($input['date']) && isset($input['category']) && isset($input['merchant']) && isset($input['amount'])) {
            $id = mysqli_real_escape_string($conn, $input['id']);
            $date = mysqli_real_escape_string($conn, $input['date']);
            $category = mysqli_real_escape_string($conn, $input['category']);
            $merchant = mysqli_real_escape_string($conn, $input['merchant']);
            $amount = mysqli_real_escape_string($conn, $input['amount']);

            $sql = "UPDATE transactions SET date='$date', category='$category', merchant='$merchant', amount='$amount' WHERE id='$id'";
            $result = mysqli_query($conn, $sql);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incomplete input']);
        }
        break;

    case 'DELETE':
        if (isset($input['id'])) {
            $id = mysqli_real_escape_string($conn, $input['id']);
            $sql = "DELETE FROM transactions WHERE id='$id'";
            $result = mysqli_query($conn, $sql);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incomplete input']);
        }
        break;
}

// Close MySQL connection
if (isset($conn)) {
    mysqli_close($conn);
}
?>
