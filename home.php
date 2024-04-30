<?php 
// Error reporting settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION['role']))
{
    header("location: login.php"); die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Home Page</title>
</head>
<body class="bg-dark">

<?php
// Include the navigation bar
include 'navbar.php';
?>

<!-- Main content -->
<div class="container">
  <div class="row py-5">
    <!-- Low risk assets -->
    <div class="col-md-4 ">
        <img src="images/low.png" class="img img-fluid rounded" alt="">
        <a href="assets.php?risk=low" class="btn btn-danger mx-auto d-block my-2">Low Risk Assets</a>
    </div>
    <!-- Medium risk assets -->
    <div class="col-md-4">
        <img src="images/medium.png" class="img img-fluid rounded" alt="">
        <a href="assets.php?risk=medium" class="btn btn-danger mx-auto d-block my-2">Medium Risk Assets</a>
    </div>
    <!-- High risk assets -->
    <div class="col-md-4">
        <img src="images/high.png" class="img img-fluid rounded" alt="">
        <a href="assets.php?risk=high" class="btn btn-danger mx-auto d-block my-2">High Risk Assets</a>
    </div>
  </div>
</div>

<div class="card bg-dark">
    <?php
    // Include the navigation bar
    include 'HPdashboard.php';
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
