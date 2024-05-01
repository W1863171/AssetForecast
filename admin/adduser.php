<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = $_POST['firstName'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $managerID = $_POST['managerID'];

    // Get the current date and time
    $createdAt = date("Y-m-d H:i:s");

    // Establish database connection
    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute SQL query to insert new user
    $sql = "INSERT INTO user (firstName, surname, email, password, role, managerID, createdAt) 
            VALUES ('$firstName', '$surname', '$email', '$password', '$role', '$managerID', '$createdAt')";

    if (mysqli_query($conn, $sql)) {
        echo "New user created successfully";

        // Retrieve the inserted user's ID
        $user_id = mysqli_insert_id($conn);
        
        //adding to relevant role tables 
        // Check if the role is 'engineer'
        if ($role == 'engineer') {
            // Prepare and execute SQL query to insert into engineer table
            $engineer_sql = "INSERT INTO engineer (userID, lastTrainingDate) VALUES ('$user_id', '$createdAt')";
            if (mysqli_query($conn, $engineer_sql)) {
                echo "User assigned as engineer successfully";
            } else {
                echo "Error assigning user as engineer: " . mysqli_error($conn);
            }
        }
        // Check if the role is 'operator'
        if ($role == 'operator') {
            // Prepare and execute SQL query to insert into operator table
            $operator_sql = "INSERT INTO operator (userID) VALUES ('$user_id')";
            if (mysqli_query($conn, $operator_sql)) {
                echo "User assigned as operator successfully";
            } else {
                echo "Error assigning user as operator: " . mysqli_error($conn);
            }
        }
        // Check if the role is 'scheduler'
        if ($role == 'scheduler') {
            // Prepare and execute SQL query to insert into scheduler table
            $scheduler_sql = "INSERT INTO scheduler (userID) VALUES ('$user_id')";
            if (mysqli_query($conn, $scheduler_sql)) {
                echo "User assigned as scheduler successfully";
            } else {
                echo "Error assigning user as scheduler: " . mysqli_error($conn);
            }
        }
        // Check if the role is 'facilities manager'
        if ($role == 'facilities manager') {
            // Prepare and execute SQL query to insert into facilities manager table
            $facilitiesmanager_sql = "INSERT INTO facilities_manager (userID) VALUES ('$user_id')";
            if (mysqli_query($conn, $facilitiesmanager_sql)) {
                echo "User assigned as facilities manager successfully";
            } else {
                echo "Error assigning user as facilities manager: " . mysqli_error($conn);
            }
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
<?php
// Include the navigation bar
include 'adminnavbar.php';
?>
    <div class="container mt-5">
        <h1>Add User</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name:</label>
                <input type="text" id="firstName" name="firstName" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="surname" class="form-label">Surname:</label>
                <input type="text" id="surname" name="surname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role:</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <?php
                    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                    $query = "SELECT DISTINCT role FROM user";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['role'] . "'>" . $row['role'] . "</option>";
                    }

                    mysqli_close($conn);
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="managerID" class="form-label">Manager:</label>
                <select name="managerID" id="managerID" class="form-select">
                    <option value="">Select Manager</option>
                    <?php
                    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                    $query = "SELECT userID, CONCAT(firstName, ' ', surname) AS fullName FROM user WHERE role = 'admin' OR role = 'user' OR role = 'facilities manager'";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['userID'] . "'>" . $row['fullName'] . "</option>";
                    }

                    mysqli_close($conn);
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>
</body>
</html>
