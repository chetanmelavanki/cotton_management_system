<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM agent WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent = $result->fetch_assoc();

    if ($agent) {
        $reset_code = rand(100000, 999999); // Simulated OTP
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $reset_code;

        // Simulate sending email (show it for development)
        $success = "Your OTP code is: <strong>$reset_code</strong> (For demo only)";
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center mb-4">Forgot Password</h2>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success text-center"><?= $success ?></div>
    <div class="text-center">
      <a href="reset_password.php" class="btn btn-success mt-2">Go to Reset Page</a>
    </div>
  <?php elseif (!empty($error)): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="mx-auto shadow p-4 bg-light" style="max-width: 400px;">
    <div class="mb-3">
      <label for="email" class="form-label">Enter Your Registered Email:</label>
      <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100" type="submit">Send OTP</button>
  </form>
</div>

</body>
</html>
