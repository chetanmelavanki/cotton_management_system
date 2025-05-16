<?php
session_start();
include('db.php');

// Check if purchase ID is passed
if (!isset($_GET['purchase_id'])) {
    echo "No purchase found.";
    exit();
}

$purchase_id = $_GET['purchase_id'];

// Fetch purchase details (now includes purchaser_id and agent_id)
$stmt = $conn->prepare("SELECT p.purchase_id, p.purchaser_id, p.agent_id, c.type AS cotton_type, p.quantity, p.price, p.total_amount, p.purchase_date, pu.name AS purchaser_name, a.name AS agent_name
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

// Process Payment (Mock Payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_status = 'Success';  // As per the new requirement

    // Insert payment record with 'Success' status
    $payment_stmt = $conn->prepare("INSERT INTO payment (purchaser_id, agent_id, amount, transaction_id, payment_mode, status, payment_date)
                                    VALUES (?, ?, ?, ?, ?, ?, CURDATE())");

    $transaction_id = uniqid('txn_');  // Mock transaction ID
    $payment_mode = 'UPI';  // Mock payment mode

    $payment_stmt->bind_param(
        "iiisss",
        $purchase['purchaser_id'],
        $purchase['agent_id'],
        $purchase['total_amount'],
        $transaction_id,
        $payment_mode,
        $payment_status
    );

    if ($payment_stmt->execute()) {
        // Update the purchase table to mark the payment status as 'Success'
        $purchase_update = $conn->prepare("UPDATE purchase SET payment_status = ? WHERE purchase_id = ?");
        $purchase_update->bind_param("si", $payment_status, $purchase_id);

        if ($purchase_update->execute()) {
            // Redirect to purchase slip generation page
            header("Location: purchase_slip.php?purchase_id=" . $purchase_id);
            exit();
        } else {
            echo "Error updating purchase payment status: " . $conn->error;
        }
    } else {
        echo "Error inserting payment record: " . $conn->error;
    }
}
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">Payment for Lot No: <?= htmlspecialchars($purchase['purchase_id'] ?? '') ?></h2>

    <h4>Total: ₹<?= htmlspecialchars($purchase['total_amount'] ?? 0) ?></h4>

    <form method="POST">
        <button type="submit" class="btn btn-primary w-100">Make Payment</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
