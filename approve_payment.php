<?php
session_start();
include 'db.php';

// Check if agent is logged in
if (!isset($_SESSION['agent_id'])) {
    echo "Please log in as an agent.";
    exit();
}

$agent_id = $_SESSION['agent_id'];
$payment_id = $_GET['payment_id'];

// Update the payment status to 'Success'
$stmt = $conn->prepare("UPDATE payment SET status = 'Success' WHERE payment_id = ?");
$stmt->bind_param("i", $payment_id);

if ($stmt->execute()) {
    echo "Payment approved successfully.";
} else {
    echo "Error approving payment.";
}
?>
