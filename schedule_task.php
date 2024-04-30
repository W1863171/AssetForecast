<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check if the user is logged in and has the appropriate role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'scheduler') {
    echo "You do not have permission to perform this action.";
    exit;
}

// Check if the task ID, engineer ID, and scheduled date are provided in the POST data
if (isset($_POST['taskID'])){
    if(isset($_POST['engineerID'])){
        if(isset($_POST['scheduledDate'])) {

            // Sanitize the input
            $taskID = $_POST['taskID'];
            $engineerID = $_POST['engineerID'];
            $scheduledDate = $_POST['scheduledDate'];
            $userID = $_SESSION['userID']; 

            // check current task status
            $sql = "SELECT taskStatus FROM maintenance_task WHERE taskID = '$taskID'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);

            if ($row['taskStatus']=='WorkDone' || $row['taskStatus']=='Finished' || $row['taskStatus']=='Not Approved' ) {
                echo "This task cannot be assigned as it is either not approved, Workdone or Finished.";
                exit;
            } else {

                // Update the maintenance_task table to set the task status to 'Released' and approvedBy to the current user's userID
                $updateQuery = "UPDATE maintenance_task SET taskStatus = 'Assigned', scheduledBy = '$userID', scheduledDate = '$scheduledDate', attendedBy = '$engineerID' WHERE taskID = '$taskID' AND (taskStatus != 'WorkDone' AND taskStatus != 'Finished' AND taskStatus != 'Not Approved')";

                if (mysqli_query($conn, $updateQuery)) {
                    // If the update is successful, send a success message
                    echo "Task scheduled successfully.";
                } else {
                    // If there's an error with the update query, send an error message
                    echo "Error updating task assignment: " . mysqli_error($conn);
                }
            }
        } else {
            // If scheduled date is not provided, send an error message
            echo "Scheduled Date not provided.";
        }
    } else {
        // If engineerID is not provided, send an error message
        echo "Engineer ID not provided.";
    }
} else {
    // If task ID is not provided, send an error message
    echo "Task ID not provided.";
}
?>