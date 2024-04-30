<?php
// Error reporting settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the POST request
    $noteText = $_POST['editNoteText'];
    $noteID = $_POST['editNoteID']; // Assuming noteID is passed from the form

    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL statement to update the note in the database
    $sql = "UPDATE asset_note 
            SET note = ?, lastEditedBy = ?, lastEditDate = NOW()
            WHERE noteID = ?";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "sii", $noteText, $lastEditedBy, $noteID);

        // Set the parameter values
        $lastEditedBy = $_SESSION['userID']; 
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // If the note was successfully edited, return a success message
            echo "Note edited successfully";
        } else {
            // If there was an error executing the statement, return an error message
            echo "Error executing statement: " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // If there was an error preparing the statement, return an error message
        echo "Error preparing statement: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // If the script is accessed directly without a valid request method, display an error message
    echo "Invalid request method";
}
?>
