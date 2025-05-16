<?php
session_start();
include('db.php');

// Check if purchase ID is passed
if (!isset($_GET['purchase_id'])) {
    echo "No purchase found.";
    exit();
}

$purchase_id = $_GET['purchase_id'];

// Fetch purchase details
$stmt = $conn->prepare("SELECT p.purchase_id, c.type AS cotton_type, p.quantity, p.price, p.total_amount, p.purchase_date, pu.name AS purchaser_name, a.name AS agent_name
                        FROM purchase p
                        JOIN cotton c ON p.cotton_id = c.cotton_id
                        JOIN purchaser pu ON p.purchaser_id = pu.purchaser_id
                        JOIN agent a ON p.agent_id = a.agent_id
                        WHERE p.purchase_id = ?");
$stmt->bind_param("i", $purchase_id);
$stmt->execute();
$result = $stmt->get_result();
$purchase = $result->fetch_assoc();

if (!$purchase) {
    echo "Invalid purchase ID.";
    exit();
}

// Include TCPDF library
require_once('libs/tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Purchase Invoice');

// Set default header data
$pdf->SetHeaderData('', 0, 'Cotton Management System', 'Purchase Invoice');
$pdf->setHeaderFont(Array('helvetica', '', 10));
$pdf->setFooterFont(Array('helvetica', '', 8));
$pdf->SetMargins(15, 27, 15);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();

// Add Purchase Details
$html = '
<h2>Purchase Invoice</h2>
<table cellpadding="5">
    <tr><td><strong>Purchase ID:</strong></td><td>' . $purchase['purchase_id'] . '</td></tr>
    <tr><td><strong>Purchaser Name:</strong></td><td>' . htmlspecialchars($purchase['purchaser_name']) . '</td></tr>
    <tr><td><strong>Agent Name:</strong></td><td>' . htmlspecialchars($purchase['agent_name']) . '</td></tr>
    <tr><td><strong>Cotton Type:</strong></td><td>' . htmlspecialchars($purchase['cotton_type']) . '</td></tr>
    <tr><td><strong>Quantity (kg):</strong></td><td>' . $purchase['quantity'] . '</td></tr>
    <tr><td><strong>Price per kg (₹):</strong></td><td>' . $purchase['price'] . '</td></tr>
    <tr><td><strong>Total Amount (₹):</strong></td><td>' . $purchase['total_amount'] . '</td></tr>
    <tr><td><strong>Purchase Date:</strong></td><td>' . $purchase['purchase_date'] . '</td></tr>
</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF (download)
$pdf->Output('purchase_invoice_' . $purchase['purchase_id'] . '.pdf', 'D');
?>
