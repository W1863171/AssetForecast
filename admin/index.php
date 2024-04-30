<?php 

session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin')
{
    header("location: ../login.php"); die();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Dashboard</title>
</head>
<body class="bg-dark text-white">


<?php
// Include the navigation bar
include 'adminnavbar.php';
?>


    <div class="container">
        <div class="row py-5">
            <div class="col-md-4">
                <a href="assets.php" class="btn btn-danger w-100 py-5">Assets</a>
            </div>
            <div class="col-md-4">
                <a href="users.php" class="btn btn-danger w-100 py-5">Users</a>
            </div>
            <div class="col-md-4">
                <a href="buildings.php" class="btn btn-danger w-100 py-5">Buildings</a>
            </div>
        </div>
        <div class="row py-5">
            <div class="col-md-4">
                <a href="../Readings.php" class="btn btn-danger w-100 py-5">Readings</a>
            </div>
            <div class="col-md-4">
                <a href="Locations.php" class="btn btn-danger w-100 py-5">Locations</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row py-5">
            <div class="col-md-4">
                <a href="../addasset.php" class="btn btn-info w-100 py-5">Add Asset</a>
            </div>
            <div class="col-md-4">
                <a href="adduser.php" class="btn btn-info w-100 py-5">Add New User</a>
            </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
