<?php
session_start();
include('db.php');

// Redirect if not logged in as agent
if (!isset($_SESSION['agent_id'])) {
    header("Location: login_agent.php");
    exit();
}

$agent_id = $_SESSION['agent_id'];
$success = "";
$error = "";

// Fetching total commission for the current month
$current_month = date('Y-m');  // e.g., '2025-05'
$commission_query = $conn->prepare("SELECT SUM(agent_commission) AS total_commission
                                    FROM purchase 
                                    WHERE agent_id = ? AND DATE_FORMAT(purchase_date, '%Y-%m') = ?");
$commission_query->bind_param("is", $agent_id, $current_month);
$commission_query->execute();
$commission_result = $commission_query->get_result();
$commission_data = $commission_result->fetch_assoc();
$total_commission = $commission_data['total_commission'] ?? 0;

// Handle cotton upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = $_POST['farmer_id'];
    $type = htmlspecialchars(trim($_POST['type']));
    $trash = $_POST['trash'];
    $moisture = $_POST['moisture'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = $_POST['quantity'];
    $price_per_kg = $_POST['price_per_kg'];
    $produce_date = $_POST['produce_date'];

    if (!DateTime::createFromFormat('Y-m-d', $produce_date)) {
        $error = "Invalid date format. Please use YYYY-MM-DD.";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $img_name = basename($_FILES['image']['name']);
            $target = "assets/images/" . $img_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $stmt = $conn->prepare("INSERT INTO cotton (
                    farmer_id, agent_id, type, trash, moisture, description,
                    quantity, price_per_kg, produce_date, image_url
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "iissdssdss",
                    $farmer_id,
                    $agent_id,
                    $type,
                    $trash,
                    $moisture,
                    $description,
                    $quantity,
                    $price_per_kg,
                    $produce_date,
                    $target
                );

                if ($stmt->execute()) {
                    $success = "Cotton details uploaded successfully.";
                } else {
                    $error = "Failed to upload cotton details. Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Image file is required.";
        }
    }
}
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">Agent Dashboard - Upload Cotton</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <h3 class="text-center mb-4">Your Monthly Commission</h3>
    <div class="alert alert-info text-center">
        <strong>Total Commission for the Month (<?= date('F Y') ?>): ₹<?= number_format($total_commission, 2) ?></strong>
    </div>

    <form method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
        <div class="mb-3">
            <label>Farmer ID:</label>
            <input type="number" name="farmer_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Type of Cotton:</label>
            <input type="text" name="type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Trash (%):</label>
            <input type="number" step="0.01" name="trash" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Moisture (%):</label>
            <input type="number" step="0.01" name="moisture" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description:</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label>Quantity (kg):</label>
            <input type="number" step="0.01" name="quantity" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Price per kg (₹):</label>
            <input type="number" step="0.01" name="price_per_kg" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Produce Date:</label>
            <input type="date" name="produce_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Cotton Image:</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Upload Cotton</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
