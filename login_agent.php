<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM agent WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent = $result->fetch_assoc();

    // Direct password check (only for development/testing)
    if ($agent && $password === $agent['password']) {
        $_SESSION['agent_id'] = $agent['agent_id'];
        $_SESSION['agent_name'] = $agent['name'];

        // For navbar handling
        $_SESSION['user_id'] = $agent['agent_id'];
        $_SESSION['user_type'] = 'Agent';

        header("Location: agent_dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Agent Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center mb-4">Agent Login</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="mx-auto shadow p-4 bg-light" style="max-width: 400px;">
    <div class="mb-3">
      <label for="email" class="form-label">Email:</label>
      <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password:</label>
      <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <button class="btn btn-primary w-100" type="submit">Login</button>
    <!-- Add this below the submit button inside your form -->
<div class="text-center mt-3">
    <a href="forgot_password.php">Forgot Password?</a>
</div>

  </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
