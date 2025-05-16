<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Cotton Management System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= ($_SESSION['user_type'] === 'Purchaser') ? 'purchaser_dashboard.php' : 'agent_dashboard.php' ?>">
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="monthly_report.php">Monthly Report</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login_agent.php">Login (Agent)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login_purchaser.php">Login (Purchaser)</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
