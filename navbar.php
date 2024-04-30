<?php


$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check if the user is logged in and has a valid session
if (!isset($_SESSION['userID'])) {
    echo "User session not found.";
    exit;
}

// Retrieve the user's first name and surname from the database
$userID = $_SESSION['userID'];
$engineernav = "SELECT firstName, surname FROM user WHERE userID = '$userID'";
$engineernavresult = mysqli_query($conn, $engineernav);
// Fetch the row from the result set
$eachrow = mysqli_fetch_assoc($engineernavresult);
// Retrieve the first name and surname
$engineerfirstName = $eachrow['firstName'];
$engineersurname = $eachrow['surname'];


?>


<!-- Navbar -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <div class="container-fluid">
    <!-- Logo -->
    <img src="images/logo.png" alt="" width="40">
    <a class="navbar-brand" href="home.php"> AssetForecast</a>
    <!-- Navbar toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="assets.php">Assets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="buildings.php">Buildings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="readings.php">Readings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Asset Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="userprofile.php">My Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="maintenancetasks.php">Maintenance Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="maintenancedashboard.php">Maintenance Dashboard</a>
                </li>
                <?php if ($_SESSION['role'] == 'engineer') : ?>
                    <!-- Display my assigned tasks link only for engineer users -->
                    <li class="nav-item">
                            <a class="nav-link" href=<?php echo '"maintenancetasks.php?engineer_filter=' . $eachrow['firstName'] . '+' . $eachrow['surname'] . '&taskstatus_filter=Assigned"'?>>My Assigned Tasks</a>
                        </li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'admin') : ?>
                    <!-- Display admin panel link only for admin users -->
                    <li class="nav-item">
                        <a class="nav-link" href="admin/index.php">Admin Panel</a>
                    </li>
                <?php endif; ?>
                <!-- Logout link -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
        </ul>
    </div>
  </div>
</nav>