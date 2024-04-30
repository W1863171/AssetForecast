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

// Check if the task ID is provided in the URL
if (!isset($_GET['taskID'])) {
    // Redirect back to the maintenance tasks page if no task ID is provided
    header("location: maintenancetasks.php");
    exit();
}

// Get the task ID from the URL
$taskID = $_GET['taskID'];

// Database connection
$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch task information along with associated asset, location, building, engineer, operator, and scheduler details
$sql = "SELECT 
            mt.taskID,
            mt.barcode,
            mt.taskStatus,
            mt.createdDate,
            mt.taskType,
            mt.description,
            mt.startedAt,
            mt.completedAt,
            mt.attendedBy,
            mt.approvedBy,
            mt.scheduledBy,
            mt.scheduledDate,
            CONCAT(u1.firstName, ' ', u1.surname) AS attendedByName,
            CONCAT(u2.firstName, ' ', u2.surname) AS approvedByName,
            CONCAT(u3.firstName, ' ', u3.surname) AS scheduledByName,
            a.assettype,
            l.roomcode,
            b.buildingname
        FROM 
            maintenance_task AS mt
        INNER JOIN 
            asset AS a ON mt.barcode = a.barcode
        INNER JOIN 
            location AS l ON a.locationID = l.locationID
        INNER JOIN 
            building AS b ON l.buildingID = b.buildingID
        LEFT JOIN 
            user AS u1 ON mt.attendedBy = u1.userID
        LEFT JOIN 
            user AS u2 ON mt.approvedBy = u2.userID
        LEFT JOIN 
            user AS u3 ON mt.scheduledBy = u3.userID
        WHERE
            mt.taskID = '$taskID'";

$result = mysqli_query($conn, $sql);

// Check if the task exists
if (mysqli_num_rows($result) == 0) {
    // Redirect back to the maintenance tasks page if the task does not exist
    header("location: maintenance_tasks.php");
    exit();
}

// Fetch the task information
$task = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Maintenance Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
    <?php
    // Include the navigation bar
    include 'navbar.php';
    ?>
    <div class="container mt-5">
        <h1>View Maintenance Task - Task ID: <?php echo $task['taskID']; ?></h1>
        <hr>
        <div class="card border-dark mb-3">
            <div class="card-body">
                <h3 class="card-title">Task Information</h3>
                <hr>
                <p><strong>Task ID:</strong> <?php echo $task['taskID']; ?></p>
                <p><strong>Asset Barcode:</strong> <?php echo $task['barcode']; ?></p>
                <p><strong>Task Status:</strong> <?php echo $task['taskStatus']; ?></p>
                <p><strong>Created Date:</strong> <?php echo $task['createdDate']; ?></p>
                <p><strong>Task Type:</strong> <?php echo $task['taskType']; ?></p>
                <p><strong>Description:</strong> <?php echo $task['description']; ?></p>
                <p><strong>Started At:</strong> <?php echo $task['startedAt']; ?></p>
                <p><strong>Completed At:</strong> <?php echo $task['completedAt']; ?></p>
                <p><strong>Attended By:</strong> <?php echo $task['attendedByName']; ?></p>
                <p><strong>Approved By:</strong> <?php echo $task['approvedByName']; ?></p>
                <p><strong>Scheduled By:</strong> <?php echo $task['scheduledByName']; ?></p>
                <p><strong>Scheduled Date:</strong> <?php echo $task['scheduledDate']; ?></p>
                <p><strong>Asset Type:</strong> <?php echo $task['assettype']; ?></p>
                <p><strong>Room Code:</strong> <?php echo $task['roomcode']; ?></p>
                <p><strong>Building Name:</strong> <?php echo $task['buildingname']; ?></p>
            </div>
        </div>
        <?php

            // Check if the user is an engineer
            $isEngineer = $_SESSION['role'] == 'engineer';

            // Check if the task status allows updates
            $allowedStatus = !in_array($task['taskStatus'], ['Work Done', 'Finished', 'Reported']);

            // Display the update button if the user is an engineer and the status allows updates
            if ($isEngineer && $allowedStatus) {
                echo '<a href="updatemaintenancetask.php?taskID=' . $taskID . '" class="btn btn-primary">Update Task</a>';
            }
            ?>
        <hr>
        
        </hr>
        <!-- Comments Box -->
        <h3 class="card-title">Comments</h3>
        <hr>
        <!-- Fetch comments from the database -->
        <?php
        // Query to fetch comments for the task
        $comment_sql = "SELECT comments FROM maintenance_task WHERE taskID = '$taskID'";
        $comment_result = mysqli_query($conn, $comment_sql);
        if (mysqli_num_rows($comment_result) > 0) {
            // Fetch the comments
            $comments_row = mysqli_fetch_assoc($comment_result);
            $comments = $comments_row['comments'];
        } else {
            $comments = ''; // If no comments found, set empty string
        }
        ?>
        <!-- Comments textarea prepopulated with database comments -->
        <textarea class="form-control" rows="3" placeholder="Add your comments here"><?php echo $comments; ?></textarea>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
