<?php
session_start();
include('db.php');

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $registered_date = date('Y-m-d');

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM purchaser WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $error = "Email already registered. Please login.";
    } else {
        $stmt = $conn->prepare("INSERT INTO purchaser (name, location, contact_number, email, password, registered_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $location, $contact_number, $email, $password, $registered_date);
        if ($stmt->execute()) {
            $success = "Registration successful. You can now login.";
        } else {
            $error = "Registration failed. Try again.";
        }
    }
}
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Purchaser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Purchaser Registration</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Location:</label>
            <input type="text" name="location" class="form-control">
        </div>
        <div class="mb-3">
            <label>Contact Number:</label>
            <input type="text" name="contact_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
