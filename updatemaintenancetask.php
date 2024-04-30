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

// Check if the user is an engineer
$isEngineer = $_SESSION['role'] == 'engineer';

// Database connection
$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch task information
$taskID = $_GET['taskID']; 

// Check if the taskID is numeric
if (!is_numeric($taskID)) {
    // Redirect back to maintenance tasks
    header("location: maintenancetasks.php");
    exit();
}

$sql = "SELECT mt.barcode, mt.comments, mt.taskStatus FROM maintenance_task mt WHERE mt.taskID = $taskID";
$result = mysqli_query($conn, $sql);

// Check if task information is found
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $barcode = $row['barcode'];
    $comments = $row['comments'];
    $taskStatus = $row['taskStatus'];
    
    // Check if the task status allows updates
    $allowedStatus = !in_array($taskStatus, ['Work Done', 'Finished', 'Reported']);
    
    // Check if the engineer is authorized to update the task
    if (!$isEngineer || !$allowedStatus) {
        // Redirect back to maintenance tasks
        header("location: maintenancetasks.php");
        exit();
    }
} else {
    // Handle case where task information is not found
    // Redirect back to maintenance tasks
    header("location: maintenancetasks.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $taskID = $_POST['taskID']; 
    $startedAt = $_POST['startedDateTime'];
    $completedAt = $_POST['completedDateTime'];
    $newStatus = $_POST['newStatus']; 
    $comments = $_POST['comments']; 

    // Update database with new task details
    if ($startedAt === '') {
        $startedAtValue = "NULL";
    } else {
        $startedAtValue = "'$startedAt'";
    }

    if ($completedAt === '') {
        $completedAtValue = "NULL";
    } else {
        $completedAtValue = "'$completedAt'";
    }

    $sql = "UPDATE maintenance_task SET startedAt = $startedAtValue, completedAt = $completedAtValue, taskStatus = '$newStatus', comments = '$comments' WHERE taskID = $taskID";

    if (mysqli_query($conn, $sql)) {
        // Database updated successfully
        // Redirect to addreading.php with the appropriate barcode
        header("Location: addreading.php?barcode=" . $barcode); 
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Maintenance Task - </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>Update Maintenance Task - <?php echo $taskID; ?></h1>
        <hr>
        <form action="updatemaintenancetask.php?taskID=<?php echo $taskID; ?>" method="post">
            <!-- Hidden input field for task ID -->
            <input type="hidden" name="taskID" value="<?php echo $taskID; ?>">

            <!-- Started Date and Time -->
            <div class="mb-3">
                <label for="startedDateTime" class="form-label">Started Date and Time:</label>
                <input type="datetime-local" class="form-control" id="startedDateTime" name="startedDateTime" >
            </div>

            <!-- Completed Date and Time -->
            <div class="mb-3">
                <label for="completedDateTime" class="form-label">Completed Date and Time:</label>
                <input type="datetime-local" class="form-control" id="completedDateTime" name="completedDateTime" >
            </div>

            <!-- New Status -->
            <div class="mb-3">
                <label for="newStatus" class="form-label">New Status:</label>
                <input type="text" class="form-control" id="newStatus" name="newStatus" >
            </div>

            <!-- Comments -->
            <div class="mb-3">
                <label for="comments" class="form-label">Comments:</label>
                <textarea class="form-control" id="comments" name="comments" rows="3"><?php echo $comments; ?></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <!-- Button to add a new reading -->
        <?php if ($isEngineer && $allowedStatus): ?>
            <a href="addreading.php?barcode=<?php echo $barcode; ?>" class="btn btn-success mt-3">Add New Reading</a>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
