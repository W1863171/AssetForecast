<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check if the user is logged in and has the appropriate role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'operator') {
    echo "You do not have permission to perform this action.";
    exit;
}

// Check if the task ID is provided in the POST data
if (isset($_POST['taskID'])) {
    // Connect to the database

    // Sanitize the input
    $taskID = mysqli_real_escape_string($conn, $_POST['taskID']);
    $userID = $_SESSION['userID']; 

    // Update the maintenance_task table to set the task status to 'Released' and approvedBy to the current user's userID
    $updateQuery = "UPDATE maintenance_task SET taskStatus = 'Released', approvedBy = '$userID' WHERE taskID = '$taskID'";

    if (mysqli_query($conn, $updateQuery)) {
        // If the update is successful, send a success message
        echo "Task approved successfully.";
    } else {
        // If there's an error with the update query, send an error message
        echo "Error updating task status: " . mysqli_error($conn);
    }
} else {
    // If task ID is not provided, send an error message
    echo "Task ID not provided.";
}
?>
