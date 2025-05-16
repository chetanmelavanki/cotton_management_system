<?php
session_start();
include('db.php');

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];
    $new_password = $_POST['new_password'];

    if ($entered_otp == $_SESSION['reset_otp']) {
        $email = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE agent SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        if ($stmt->execute()) {
            $success = "Password reset successfully. <a href='login_agent.php'>Login Now</a>";
            unset($_SESSION['reset_otp']);
            unset($_SESSION['reset_email']);
        } else {
            $error = "Failed to reset password.";
        }
    } else {
        $error = "Invalid OTP.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Reset Password</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto shadow p-4 bg-light" style="max-width: 400px;">
        <div class="mb-3">
            <label for="otp" class="form-label">Enter OTP:</label>
            <input type="number" name="otp" id="otp" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password:</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>
        <button class="btn btn-success w-100" type="submit">Reset Password</button>
    </form>
</div>
</body>
</html>
