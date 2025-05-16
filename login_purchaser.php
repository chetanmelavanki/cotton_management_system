<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM purchaser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchaser = $result->fetch_assoc();

    if ($purchaser && password_verify($password, $purchaser['password'])) {
        $_SESSION['purchaser_id'] = $purchaser['purchaser_id'];
        $_SESSION['purchaser_name'] = $purchaser['name'];

        // Common session variables for navbar
        $_SESSION['user_id'] = $purchaser['purchaser_id'];
        $_SESSION['user_type'] = 'Purchaser';

        header("Location: purchaser_dashboard.php");
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
  <title>Purchaser Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center">Purchaser Login</h2>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>
  <form method="POST" class="mx-auto" style="max-width: 400px;">
    <div class="mb-3">
      <label>Email:</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Password:</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-success w-100" type="submit">Login</button>
  </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
