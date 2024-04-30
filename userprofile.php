<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve user information
$userID = $_SESSION['userID'];
$sql = "SELECT * FROM user WHERE userID = $userID";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error: " . $sql . "<br>" . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
} else {
    die("User not found");
}

// Update user information if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    // Get the current date and time
    $updatedAt = date("Y-m-d H:i:s");

    // Update user information
    $update_sql = "UPDATE user SET firstName='$firstName', surname='$surname', email='$email', password='$password', updatedAt='$updatedAt' WHERE userID=$userID";

    if (mysqli_query($conn, $update_sql)) {
        echo "Profile updated successfully";
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
<?php
// Include the navigation bar
include 'navbar.php';
?>

<div class="container mt-5">
    <h1>User Profile</h1>
    <form action="" method="post">
        <div class="mb-3">
            <label for="firstName" class="form-label">First Name:</label>
            <input type="text" id="firstName" name="firstName" class="form-control" value="<?php echo $row['firstName']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="surname" class="form-label">Surname:</label>
            <input type="text" id="surname" name="surname" class="form-control" value="<?php echo $row['surname']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo $row['email']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
</body>
</html>
