<?php
// Error reporting settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the POST request
    $noteText = $_POST['noteText'];
    $assetID = $_POST['barcode']; // Accessing barcode from the form data

    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL statement to insert the note into the database
    $sql = "INSERT INTO asset_note (note, assetID, createdBy, creationDate)
            VALUES (?, ?, ?, NOW())"; // Using NOW() to get the current date and time

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "sii", $noteText, $assetID, $createdBy);

        // Set the parameter values
        $createdBy = $_SESSION['userID']; // Assuming $_SESSION['userID'] is set properly

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // If the note was successfully added, return a success message
            echo "Note added successfully";
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
