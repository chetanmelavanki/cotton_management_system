<?php
session_start();
include('db.php');

// Simple authentication placeholder
// You should replace this with real admin login logic
$isAdmin = true;
if (!$isAdmin) {
    echo "Access denied.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_id'])) {
    $purchase_id = $_POST['purchase_id'];
    $action = $_POST['action'];

    $status = ($action === 'approve') ? 'Completed' : 'Pending'; // Can add 'Rejected' if needed
    $stmt = $conn->prepare("UPDATE purchase SET payment_status = ? WHERE purchase_id = ?");
    $stmt->bind_param("si", $status, $purchase_id);
    $stmt->execute();
}

// Fetch pending payments
$query = "SELECT p.purchase_id, p.lot_number, p.purchase_date, p.total_amount, pu.name AS purchaser_name, a.name AS agent_name
          FROM purchase p
          JOIN purchaser pu ON p.purchaser_id = pu.purchaser_id
          JOIN agent a ON p.agent_id = a.agent_id
          WHERE p.payment_status = 'Pending'";
$result = $conn->query($query);
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Payment Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Pending Payments</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Lot No</th>
                <th>Purchase Date</th>
                <th>Purchaser</th>
                <th>Agent</th>
                <th>Total Amount (₹)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['lot_number'] ?></td>
                    <td><?= $row['purchase_date'] ?></td>
                    <td><?= htmlspecialchars($row['purchaser_name']) ?></td>
                    <td><?= htmlspecialchars($row['agent_name']) ?></td>
                    <td><?= $row['total_amount'] ?></td>
                    <td>
                        <form method="POST" class="d-flex gap-1">
                            <input type="hidden" name="purchase_id" value="<?= $row['purchase_id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No pending payments</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
