<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = $_POST['otp'];
    $new_password = $_POST['new_password'];

    if ($entered_code == $_SESSION['reset_code']) {
        $email = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE agent SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        $success = "Password reset successful. <a href='login_agent.php'>Login now</a>";
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
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center mb-4">Reset Password</h2>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success text-center"><?= $success ?></div>
  <?php elseif (!empty($error)): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="mx-auto shadow p-4 bg-light" style="max-width: 400px;">
    <div class="mb-3">
      <label for="otp" class="form-label">Enter OTP:</label>
      <input type="text" name="otp" id="otp" class="form-control" required>
    </div>

    <div class="mb-3 position-relative">
      <label for="new_password" class="form-label">New Password:</label>
      <input type="password" name="new_password" id="new_password" class="form-control" required>
      <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;" onclick="toggleNewPassword()">👁️</span>
    </div>

    <button class="btn btn-success w-100" type="submit">Reset Password</button>
  </form>
</div>

<script>
function toggleNewPassword() {
  const field = document.getElementById("new_password");
  field.type = field.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
