<!-- Navbar -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <div class="container-fluid">
    <!-- Logo -->
    <img src="images/logo.png" alt="" width="40">
    <a class="navbar-brand" href="../home.php"> AssetForecast</a>
    <!-- Navbar toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav text-danger">
                <li class="nav-item">
                    <a class="nav-link" href="../assets.php">Assets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../buildings.php">Buildings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../readings.php">Readings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php">Asset Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../userprofile.php">My Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../maintenancetasks.php">Maintenance Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../maintenancedashboard.php">Maintenance Dashboard</a>
                </li>
                <?php if ($_SESSION['role'] == 'admin') : ?>
                    <!-- Display admin panel link only for admin users -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Admin Panel</a>
                    </li>
                <?php endif; ?>
                <!-- Logout link -->
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
        </ul>
    </div>
  </div>
</nav>