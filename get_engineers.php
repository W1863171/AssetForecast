<?php
// Fetch engineers from the database
session_start();

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// query to fetch engineers
$query = "SELECT userID, CONCAT(firstName, ' ', surname) AS engineerName FROM user WHERE role = 'engineer'";
$result = mysqli_query($conn, $query);

$options = '';
while ($row = mysqli_fetch_assoc($result)) {
    $options .= "<option value='{$row['userID']}'>{$row['engineerName']}</option>";
}

echo $options;
?>
