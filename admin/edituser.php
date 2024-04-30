<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] != 'admin') {
    header("location: ../login.php");
    die();
}

if (!isset($_GET['userid']) || empty($_GET['userid'])) {
    header("location: users.php");
    die();
}

$userID = $_GET['userid'];

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Fetch user data
$user_query = "SELECT * FROM user WHERE userID = $userID";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Update user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = isset($_POST['firstname']) ? $_POST['firstname'] : '';
    $surname = isset($_POST['surname']) ? $_POST['surname'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    $managerID = isset($_POST['managerID']) ? $_POST['managerID'] : '';

    // Check if a new password is provided
    if (!empty($_POST['password'])) {

        // Update the password in the database
        $update_password_query = "UPDATE user SET password = '$password' WHERE userID = $userID";
        mysqli_query($conn, $update_password_query);
    }

    $update_query = "UPDATE user SET firstName = '$firstName', surname = '$surname', email = '$email', role = '$role' WHERE userID = $userID";
    mysqli_query($conn, $update_query);
    // Redirect to users page after updating
    header("location: users.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit User</title>
</head>
<body class="bg-dark text-white">

<?php
// Include the navigation bar
include '../navbar.php';
?>

<div class="container">
    <h1>Edit User</h1>
    <hr>
    <form method="post">
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user_data['firstName']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="surname" class="form-label">Surname</label>
            <input type="text" class="form-control" id="surname" name="surname" value="<?php echo $user_data['surname']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_data['email']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <input type="text" class="form-control" id="role" name="role" value="<?php echo $user_data['role']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
