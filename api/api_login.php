<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include database configuration
include 'config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include database configuration
include 'config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

$fld = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$key = array_shift($request);

if (isset($input)) {
    $columns = preg_replace('/[^a-z0-9_]+/i', '', array_keys($input));
    $values = array_map(function ($value) use ($conn) {
        if ($value === null) return null;
        return mysqli_real_escape_string($conn, (string)$value);
    }, array_values($input));
}

switch ($method) {
    case 'POST':
        if ($fld === 'register') {
            $username = $input['username'];
            $password = password_hash($input['password'], PASSWORD_BCRYPT);
            $email = $input['email'];

            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $id = mysqli_insert_id($conn);
                echo json_encode(['auth' => true, 'user_id' => $id]);
            } else {
                echo json_encode(['auth' => false, 'message' => 'Registration failed']);
            }
        } elseif ($fld === 'login') {
            $username = $input['username'];
            $password = $input['password'];

            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['password'])) {
                    echo json_encode(['auth' => true, 'user_id' => $user['id']]);
                } else {
                    echo json_encode(['auth' => false, 'message' => 'Invalid credentials']);
                }
            } else {
                echo json_encode(['auth' => false, 'message' => 'User not found']);
            }
        }
        break;
}

// Close MySQL connection
mysqli_close($conn);
?>


$fld = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));
$key = array_shift($request);

if (isset($input)) {
    $columns = preg_replace('/[^a-z0-9_]+/i', '', array_keys($input));
    $values = array_map(function ($value) use ($conn) {
        if ($value === null) return null;
        return mysqli_real_escape_string($conn, (string)$value);
    }, array_values($input));
}

switch ($method) {
    case 'POST':
        if ($fld === 'register') {
            $username = $input['username'];
            $password = password_hash($input['password'], PASSWORD_BCRYPT);
            $email = $input['email'];

            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $id = mysqli_insert_id($conn);
                echo json_encode(['auth' => true, 'user_id' => $id]);
            } else {
                echo json_encode(['auth' => false, 'message' => 'Registration failed']);
            }
        } elseif ($fld === 'login') {
            $username = $input['username'];
            $password = $input['password'];

            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['password'])) {
                    echo json_encode(['auth' => true, 'user_id' => $user['id']]);
                } else {
                    echo json_encode(['auth' => false, 'message' => 'Invalid credentials']);
                }
            } else {
                echo json_encode(['auth' => false, 'message' => 'User not found']);
            }
        }
        break;
}

// Close MySQL connection
mysqli_close($conn);
?>
