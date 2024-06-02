<?php
echo "Report PHP is accessible";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && 
        $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET') {
        header("Access-Control-Allow-Origin: http://localhost:8080");
        exit(0);
    }
}

require_once 'tcpdf/tcpdf.php';
require_once 'config/db.php'; // Assume db.php contains proper PDO connection setup

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Your Financial Report');
$pdf->AddPage();

// Start assembling HTML content for the PDF
$htmlContent = '<h1>Financial Report</h1>';

// Fetch User Information
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userInfo = $stmt->fetch();
$htmlContent .= "<h2>Report for: " . htmlspecialchars($userInfo['username']) . "</h2>";

// Fetch Transactions
$stmt = $pdo->prepare("SELECT date, category, amount FROM transactions WHERE user_id = ?");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();
$htmlContent .= '<h2>Transactions</h2>';
foreach ($transactions as $trans) {
    $htmlContent .= "<p>" . htmlspecialchars($trans['date']) . " - " . htmlspecialchars($trans['category']) . ": \$" . htmlspecialchars($trans['amount']) . "</p>";
}

// Fetch Budgets
$stmt = $pdo->prepare("SELECT category, spent, allowance FROM budgets WHERE user_id = ?");
$stmt->execute([$user_id]);
$budgets = $stmt->fetchAll();
$htmlContent .= '<h2>Budgets</h2>';
foreach ($budgets as $budget) {
    $htmlContent .= "<p>" . htmlspecialchars($budget['category']) . ": \$" . htmlspecialchars($budget['spent']) . " of \$" . htmlspecialchars($budget['allowance']) . "</p>";
}

// Fetch Savings Goals
$stmt = $pdo->prepare("SELECT goal_name, current_amount, target_amount FROM savings_goals WHERE user_id = ?");
$stmt->execute([$user_id]);
$savings = $stmt->fetchAll();
$htmlContent .= '<h2>Savings Goals</h2>';
foreach ($savings as $save) {
    $htmlContent .= "<p>" . htmlspecialchars($save['goal_name']) . ": \$" . htmlspecialchars($save['current_amount']) . " towards \$" . htmlspecialchars($save['target_amount']) . "</p>";
}

// Write the HTML content into the PDF
$pdf->writeHTML($htmlContent, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('financial_report.pdf', 'I'); // Output the PDF to the browser (I), use 'D' to force download
?>
