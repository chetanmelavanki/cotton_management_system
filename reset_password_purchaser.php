<?php
session_start();
include('db.php');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];
    $new_password = $_POST['new_password'];

    // Check if session variables exist
    if (!isset($_SESSION['forgot_otp']) || !isset($_SESSION['forgot_email'])) {
        $error = "Session expired. Please try again.";
    } elseif ($entered_otp == $_SESSION['forgot_otp']) {
        $email = $_SESSION['forgot_email'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE purchaser SET password = ?, otp = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        $success = "Password reset successful. <a href='login_purchaser.php'>Login now</a>";
        session_unset();
        session_destroy();
    } else {
        $error = "Invalid OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password - Purchaser</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
  <h2 class="text-center mb-4">Reset Password - Purchaser</h2>

  <?php if ($success): ?>
    <div class="alert alert-success text-center"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="shadow p-4 bg-light">
    <div class="mb-3">
      <label for="otp" class="form-label">Enter OTP</label>
      <input type="text" id="otp" name="otp" class="form-control" required>
    </div>

    <div class="mb-3 position-relative">
      <label for="new_password" class="form-label">New Password</label>
      <input type="password" id="new_password" name="new_password" class="form-control" required>
      <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;" onclick="togglePassword()">👁️</span>
    </div>

    <button class="btn btn-success w-100" type="submit">Reset Password</button>
  </form>
</div>

<script>
function togglePassword() {
  const field = document.getElementById("new_password");
  field.type = field.type === "password" ? "text" : "password";
}
</script>
</body>
</html>
