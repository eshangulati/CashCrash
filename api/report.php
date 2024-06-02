<?php
require_once('tcpdf/tcpdf.php');
include 'config/db.php'; // Your DB connection script

$user_id = $_GET['user_id']; // Get user ID from the request

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Your Financial Report');

// Add a page
$pdf->AddPage();

// Content
$htmlContent = '<h1>Financial Report</h1>';

$userInfo = $pdo->query("SELECT username FROM users WHERE id = {$user_id}")->fetch();
$htmlContent .= "<h2>Report for: {$userInfo['username']}</h2>";

\
// Fetch Transactions
$transactions = $pdo->query("SELECT * FROM transactions WHERE user_id = {$user_id}");
$htmlContent .= '<h2>Transactions</h2>';
foreach ($transactions as $trans) {
    $htmlContent .= "<p>{$trans['date']} - {$trans['category']}: \${$trans['amount']}</p>";
}

// Fetch Budgets
$budgets = $pdo->query("SELECT * FROM budgets WHERE user_id = {$user_id}");
$htmlContent .= '<h2>Budgets</h2>';
foreach ($budgets as $budget) {
    $htmlContent .= "<p>{$budget['category']}: \${$budget['spent']} of \${$budget['allowance']}</p>";
}

// Fetch Savings Goals
$savings = $pdo->query("SELECT * FROM savings_goals WHERE user_id = {$user_id}");
$htmlContent .= '<h2>Savings Goals</h2>';
foreach ($savings as $save) {
    $htmlContent .= "<p>{$save['goal_name']}: \${$save['current_amount']} towards \${$save['target_amount']}</p>";
}

// Output the HTML content
$pdf->writeHTML($htmlContent, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('financial_report.pdf', 'D');
?>
