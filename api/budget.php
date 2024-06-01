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
$input = json_decode(file_get_contents('php://input'), true);

// Create SQL based on method
switch ($method) {
    case 'GET':
        if (isset($_GET['user_id'])) {
            $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
            $sql = "SELECT * FROM budgets WHERE user_id='$user_id'";
            $result = mysqli_query($conn, $sql);
            $budgets = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $budgets[] = $row;
            }
            echo json_encode($budgets);
        } else {
            echo json_encode([]);
        }
        break;

    case 'POST':
        if (isset($input['user_id']) && isset($input['category']) && isset($input['allowance'])) {
            $user_id = mysqli_real_escape_string($conn, $input['user_id']);
            $category = mysqli_real_escape_string($conn, $input['category']);
            $allowance = mysqli_real_escape_string($conn, $input['allowance']);
        
            // Calculate the total amount spent from transactions for this category and user
            $queryTotalSpent = "SELECT SUM(amount) as totalSpent FROM transactions WHERE user_id='$user_id' AND category='$category'";
            $resultTotalSpent = mysqli_query($conn, $queryTotalSpent);
            $rowTotalSpent = mysqli_fetch_assoc($resultTotalSpent);
            $amountSpent = $rowTotalSpent['totalSpent'] ? $rowTotalSpent['totalSpent'] : 0;
        
            // Insert the new budget with the calculated amountSpent
            $sql = "INSERT INTO budgets (user_id, category, allowance, amountSpent) VALUES ('$user_id', '$category', '$allowance', '$amountSpent')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create budget']);
            }
        } else {
           echo json_encode(['success' => false, 'message' => 'Incomplete input']);
        }
        break;
        

    case 'DELETE':
        if (isset($input['id'])) {
            $id = mysqli_real_escape_string($conn, $input['id']);
            $sql = "DELETE FROM budgets WHERE id='$id'";
            $result = mysqli_query($conn, $sql);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
        }
        break;
    
    case 'PUT':
        if (isset($input['id'], $input['allowance'])) {
            $id = mysqli_real_escape_string($conn, $input['id']);
            $allowance = mysqli_real_escape_string($conn, $input['allowance']);                $sql = "UPDATE budgets SET allowance='$allowance' WHERE id='$id'";
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
