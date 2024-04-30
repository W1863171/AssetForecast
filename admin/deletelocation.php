<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Check if location ID is provided
if (!isset($_GET['locationid'])) {
    header("location: locations.php");
    exit;
}

$location_id = $_GET['locationid'];

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check if the location exists in the asset table
$asset_query = "SELECT * FROM asset WHERE locationID = '$location_id'";
$asset_result = mysqli_query($conn, $asset_query);

if (mysqli_num_rows($asset_result) > 0) {
    // Assets exist for this location, advise the user to delete the assets first
    $_SESSION['message'] = "Assets exist for this location. Please delete the assets first.";
    header("location: locations.php");
    exit;
}

// No assets exist for this location, proceed with location deletion
$delete_location_query = "DELETE FROM location WHERE locationID = '$location_id'";
if (mysqli_query($conn, $delete_location_query)) {
    $_SESSION['success'] = "Location deleted successfully.";
} else {
    $_SESSION['error'] = "Error deleting location: " . mysqli_error($conn);
}

header("location: locations.php");
exit;
?>
