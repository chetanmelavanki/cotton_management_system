<?php 
session_start();
include('db.php');

if (!isset($_SESSION['purchaser_id'])) {
    header("Location: login_purchaser.php");
    exit();
}

$purchaser_id = $_SESSION['purchaser_id'];
$success = "";
$error = "";

// Handle Purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cotton_id'])) {
    $cotton_id = $_POST['cotton_id'];
    $agent_id = $_POST['agent_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price_per_kg'];
    $total = $quantity * $price;
    $lot_number = uniqid('LOT');
    $purchase_date = date('Y-m-d');

    // Calculate commission (5% agent commission as an example)
    $agent_commission = $total * 0.05; // 5% of the total amount

    // Get current quantity
    $q = $conn->prepare("SELECT quantity FROM cotton WHERE cotton_id = ?");
    $q->bind_param("i", $cotton_id);
    $q->execute();
    $qResult = $q->get_result();
    $currentCotton = $qResult->fetch_assoc();

    if (!$currentCotton) {
        $error = "Cotton record not found.";
    } elseif ($quantity > $currentCotton['quantity']) {
        $error = "Requested quantity exceeds available stock.";
    } else {
        $current_quantity = $currentCotton['quantity'];
        $new_quantity = $current_quantity - $quantity;

        // Insert into purchase table including agent commission
        $stmt = $conn->prepare("INSERT INTO purchase (cotton_id, purchaser_id, agent_id, lot_number, purchase_date, quantity, price, total_amount, payment_status, agent_commission)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)");
        $stmt->bind_param("iiissdddi", $cotton_id, $purchaser_id, $agent_id, $lot_number, $purchase_date, $quantity, $price, $total, $agent_commission);

        if ($stmt->execute()) {
            $purchase_id = $stmt->insert_id;

            // Update cotton quantity and status
            if ($new_quantity <= 0) {
                $update = $conn->prepare("UPDATE cotton SET quantity = 0, status = 'Sold' WHERE cotton_id = ?");
                $update->bind_param("i", $cotton_id);
            } else {
                $update = $conn->prepare("UPDATE cotton SET quantity = ? WHERE cotton_id = ?");
                $update->bind_param("ii", $new_quantity, $cotton_id);
            }
            $update->execute();

            $success = "Purchase successful! Lot No: $lot_number. 
            <a href='payment.php?purchase_id=$purchase_id' class='btn btn-primary btn-sm ms-2' target='_blank'>Proceed to Payment</a>";
        } else {
            $error = "Purchase failed.";
        }
    }
}

// Get available cotton
$result = $conn->query("SELECT * FROM cotton WHERE status = 'Available' OR quantity > 0 ORDER BY produce_date DESC");
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchaser Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">Available Cotton</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php if ($row['quantity'] > 0): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow">
                        <?php if ($row['image_url']): ?>
                            <img src="<?= htmlspecialchars($row['image_url']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['type']) ?> Cotton</h5>
                            <p class="card-text">
                                <strong>Trash:</strong> <?= $row['trash'] ?>%<br>
                                <strong>Moisture:</strong> <?= $row['moisture'] ?>%<br>
                                <strong>Quantity:</strong> <?= $row['quantity'] ?> kg<br>
                                <strong>Price:</strong> ₹<?= $row['price_per_kg'] ?>/kg<br>
                                <strong>Date:</strong> <?= $row['produce_date'] ?><br>
                                <em><?= htmlspecialchars($row['description']) ?></em>
                            </p>
                            <form method="POST">
                                <input type="hidden" name="cotton_id" value="<?= $row['cotton_id'] ?>">
                                <input type="hidden" name="agent_id" value="<?= $row['agent_id'] ?>">
                                <input type="hidden" name="price_per_kg" value="<?= $row['price_per_kg'] ?>">
                                <div class="mb-2">
                                    <label>Quantity to Buy (kg):</label>
                                    <input type="number" name="quantity" class="form-control" min="1" max="<?= $row['quantity'] ?>" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Buy Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
