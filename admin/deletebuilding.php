<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Check if building ID is provided
if (!isset($_GET['buildingid'])) {
    header("location: buildings.php");
    exit;
}

$building_id = $_GET['buildingid'];

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check if locations exist for the building
$location_query = "SELECT * FROM location WHERE buildingID = '$building_id'";
$location_result = mysqli_query($conn, $location_query);

if (mysqli_num_rows($location_result) > 0) {
    // Locations exist, advise the user to delete them first
    $_SESSION['message'] = "Locations exist for this building. Please delete the locations first.";
    header("location: buildings.php");
    exit;
}

// No locations exist, proceed with building deletion
$delete_building_query = "DELETE FROM building WHERE buildingID = '$building_id'";
if (mysqli_query($conn, $delete_building_query)) {
    $_SESSION['success'] = "Building deleted successfully.";
    $_SESSION['message'] = "Building deleted successfully."; // Set success message
    header("location: buildings.php"); // Redirect back to buildings.php
    exit; 
} else {
    $_SESSION['error'] = "Error deleting building: " . mysqli_error($conn);
    $_SESSION['message'] = "Error deleting building: " . mysqli_error($conn); // Set error message
    header("location: buildings.php"); // Redirect back to buildings.php
    exit; 

}


?>


