<?php
session_start();
include('db.php');

// Simulated login check
// Replace this logic with your actual login system
if (!isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
    echo "Access denied.";
    exit();
}

$userType = $_SESSION['user_type']; // 'purchaser' or 'agent'
$userId = $_SESSION['user_id'];

$month = date('m');
$year = date('Y');

if ($userType === 'purchaser') {
    $stmt = $conn->prepare("SELECT p.purchase_id, c.type, p.quantity, p.price, p.total_amount, p.purchase_date, a.name AS agent_name 
                            FROM purchase p
                            JOIN cotton c ON p.cotton_id = c.cotton_id
                            JOIN agent a ON p.agent_id = a.agent_id
                            WHERE p.purchaser_id = ? AND MONTH(p.purchase_date) = ? AND YEAR(p.purchase_date) = ?");
} else {
    $stmt = $conn->prepare("SELECT p.purchase_id, c.type, p.quantity, p.price, p.total_amount, p.purchase_date, pu.name AS purchaser_name 
                            FROM purchase p
                            JOIN cotton c ON p.cotton_id = c.cotton_id
                            JOIN purchaser pu ON p.purchaser_id = pu.purchaser_id
                            WHERE p.agent_id = ? AND MONTH(p.purchase_date) = ? AND YEAR(p.purchase_date) = ?");
}

$stmt->bind_param("iii", $userId, $month, $year);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4 text-center"><?= ucfirst($userType) ?> - Monthly Report (<?= date('F Y') ?>)</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Purchase ID</th>
                <th>Cotton Type</th>
                <th>Quantity (kg)</th>
                <th>Price per Kg (₹)</th>
                <th>Total Amount (₹)</th>
                <th>Purchase Date</th>
                <th><?= $userType === 'purchaser' ? 'Agent' : 'Purchaser' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['purchase_id'] ?></td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['price'] ?></td>
                    <td><?= $row['total_amount'] ?></td>
                    <td><?= $row['purchase_date'] ?></td>
                    <td><?= $userType === 'purchaser' ? $row['agent_name'] : $row['purchaser_name'] ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No transactions found for this month.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
