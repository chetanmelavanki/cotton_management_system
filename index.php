<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cotton Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container mt-5">
  <div class="text-center">
    <h1 class="mb-3">Welcome to Cotton Management System</h1>
    <p class="lead">
      This platform enables commission agents to upload cotton details collected from farmers and purchasers (like textile industries or wholesalers) to browse and purchase cotton directly.
    </p>
    <div class="mt-4">
      <a href="login_agent.php" class="btn btn-primary m-2">Login as Agent</a>
      <a href="login_purchaser.php" class="btn btn-success m-2">Login as Purchaser</a>
      <a href="register_purchaser.php" class="btn btn-warning m-2">Register as Purchaser</a>
    </div>
  </div>

  <hr class="my-5">

  <div class="row text-center">
    <div class="col-md-4">
      <h4>For Agents</h4>
      <p>Upload and manage cotton inventory collected from farmers, set pricing and availability.</p>
    </div>
    <div class="col-md-4">
      <h4>For Purchasers</h4>
      <p>Browse cotton types, check availability, and make purchases directly through the dashboard.</p>
    </div>
    <div class="col-md-4">
        <h4>Report Generating & Commission for Agent</h4>
        <p>Generate detailed reports and manage commissions earned by agents from cotton sales and purchases.</p>
    </div>
    <div class="col-md-12">
  <h4>Report Generating, Commission & Purchase Slip</h4>
  <p>Generate detailed reports, manage agent commissions, and create purchase slips for transactions.</p>
</div>


  </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
