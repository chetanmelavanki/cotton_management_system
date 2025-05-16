<?php
session_start();
include('db.php');
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if agent exists
    $stmt = $conn->prepare("SELECT * FROM agent WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent = $result->fetch_assoc();

    if ($agent) {
        // Generate OTP (for simplicity; use email link in production)
        $otp = rand(100000, 999999);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_email'] = $email;

        // Show OTP to user (for testing only; replace with email in production)
        $success = "Your OTP is: <strong>$otp</strong>. Use it to reset your password.";
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Forgot Password</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <div class="text-center"><a href="reset_password.php">Go to Reset Page</a></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto shadow p-4 bg-light" style="max-width: 400px;">
        <div class="mb-3">
            <label for="email" class="form-label">Registered Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Send OTP</button>
    </form>
</div>
</body>
</html>
