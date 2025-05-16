<?php
session_start();
include('db.php');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Clear previous OTP
    unset($_SESSION['forgot_otp']);
    unset($_SESSION['forgot_email']);

    // Check if email exists in purchaser table
    $stmt = $conn->prepare("SELECT * FROM purchaser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchaser = $result->fetch_assoc();

    if ($purchaser) {
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP and email in session
        $_SESSION['forgot_otp'] = $otp;
        $_SESSION['forgot_email'] = $email;

        $message = "Your OTP code is: <strong>$otp</strong> (For demo only)";
    } else {
        $error = "Email not registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forgot Password - Purchaser</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="text-center mb-4">Forgot Password - Purchaser</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success text-center"><?= $message ?></div>
        <div class="text-center">
            <a href="reset_password_purchaser.php" class="btn btn-success mt-2">Go to Reset Page</a>
        </div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="shadow p-4 bg-light">
        <div class="mb-3">
            <label for="email" class="form-label">Enter your registered Email</label>
            <input type="email" id="email" name="email" class="form-control" required />
        </div>

        <button class="btn btn-primary w-100" type="submit">Send OTP</button>
    </form>
</div>
</body>
</html>
