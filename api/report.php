<?php
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

require_once 'TCPDF-main/tcpdf.php';
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
$stmt = $pdo->prepare("SELECT merchant, date, category, amount FROM transactions WHERE user_id = ?");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();
$htmlContent .= '<h2>Transactions</h2>';
foreach ($transactions as $trans) {
    $htmlContent .= "<p>" . htmlspecialchars($trans['merchant']) . htmlspecialchars($trans['date']) . " - " . htmlspecialchars($trans['category']) . ": \$" . htmlspecialchars($trans['amount']) . "</p>";
}

// Fetch Budgets
$stmt = $pdo->prepare("SELECT category, amountSpent, allowance FROM budgets WHERE user_id = ?");
$stmt->execute([$user_id]);
$budgets = $stmt->fetchAll();
$htmlContent .= '<h2>Budgets</h2>';
foreach ($budgets as $budget) {
    $htmlContent .= "<p>" . htmlspecialchars($budget['category']) . ": \$" . htmlspecialchars($budget['amountSpent']) . " of \$" . htmlspecialchars($budget['allowance']) . "</p>";
}

// Fetch Savings Goals along with budget data
$htmlContent .= '<h2>Savings Goals</h2>';
$stmt = $pdo->prepare("SELECT sg.category, sg.goal, b.allowance, b.amountSpent 
                       FROM savings_goals sg 
                       LEFT JOIN budgets b ON sg.category = b.category AND sg.user_id = b.user_id 
                       WHERE sg.user_id = ?");
$stmt->execute([$user_id]);
$savings = $stmt->fetchAll();

foreach ($savings as $save) {
    $amountLeft = ($save['allowance'] - $save['amountSpent']) - $save['goal'];
    $htmlContent .= "<p>" . htmlspecialchars($save['category']) . ": \$" . htmlspecialchars($amountLeft) . " remaining towards the goal of \$" . htmlspecialchars($save['goal']) . "</p>";
}


// Write the HTML content into the PDF
$pdf->writeHTML($htmlContent, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('financial_report.pdf', 'I'); // Output the PDF to the browser (I), use 'D' to force download
?>
