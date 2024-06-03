<?php
$db_host = 'localhost'; // Database host
$db_user = 'root'; // Database username
$db_pass = ''; // Database password
$db_name = 'myapp'; // Database name
$charset = 'utf8mb4'; // Character set

// PDO Connection Setup
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset"; // Data Source Name
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Turn on errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
    PDO::ATTR_EMULATE_PREPARES   => false, // Enable or disable emulation of prepared statements
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// mysqli Connection Setup
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8');

?>
