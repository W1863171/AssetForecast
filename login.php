<?php 

session_start();

if(isset($_SESSION['userID']))
{
    // If userID session variable is already set, redirect to the appropriate page
    if($_SESSION['role'] == 'admin')
    {
        header("location: admin/index.php"); 
        exit();
    }
    else
    {
        header("location: home.php"); 
        exit();
    }
}

if(isset($_POST['submit']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");

    $sql = "SELECT * FROM user WHERE email = '$email' and password = '$password'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if($row != '')
    {
        // Store user information in session variables
        $_SESSION['userID'] = $row['userID'];
        $_SESSION['role'] = $row['role'];

        // Redirect to the appropriate page based on user role
        if($row['role'] == 'admin')
        {
            header("location: admin/index.php"); 
            exit();
        }
        else
        {
            header("location: home.php"); 
            exit();
        }
    }
    else
    {
        header("location: login.php"); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login Form</title>
</head>
<body>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <div class="container-fluid">
    <img src="images/logo.png" alt="" width="40">
    <a class="navbar-brand" href="index.php"> AssetForecast</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav">
        
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="text-center">Login</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-danger mx-auto d-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
