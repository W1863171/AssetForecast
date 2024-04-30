<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['role'])) {
    header("location: login.php");
    exit();
}

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'facilities manager'){
    header("location: home.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $barcode = $_POST['barcode'];
    $assetType = $_POST['assetType'];
    $assetLifeExpectancy = $_POST['assetLifeExpectancy'];
    $installationDate = $_POST['installationDate'];
    $riskscore = $_POST['riskscore'];
    $assetCondition = $_POST['assetCondition'];
    $assetRepairCategory= $_POST['assetRepairCategory'];
    $environmentalRating= $_POST['environmentalRating'];
    $impactRating = $_POST['impactRating'];
    $locationID = $_POST['locationID'];

    // Calculate asset age based on installation date
    $currentDate = date('Y-m-d');
    $installationDateTime = new DateTime($installationDate);
    $currentDateTime = new DateTime($currentDate);
    $interval = $installationDateTime->diff($currentDateTime);
    $assetAge = $interval->y; 

    // Determine risk rating based on risk score
    $riskRating = ''; // Determine risk rating based on risk score

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute SQL query to insert new asset
    $sql = "INSERT INTO asset (barcode, assetType, assetLifeExpectancy, installationDate, assetAge, riskscore, riskrating, assetCondition, assetRepairCategory, environmentalRating, impactRating, locationID) 
            VALUES ('$barcode', '$assetType', '$assetLifeExpectancy', '$installationDate', '$assetAge', '$riskscore', '$riskRating', '$assetCondition', '$assetRepairCategory', '$environmentalRating', '$impactRating', '$locationID')";

    if (mysqli_query($conn, $sql)) {

        echo "New asset added successfully";
        // Invoke the Python script to recalculate the risk score and capture the message
        $command = '../AssetForecast/python ../AssetForecast/predictriskscore.py ' . escapeshellarg($barcode);
        $output = shell_exec($command);
        
        
        // Display new risk score
        echo "$output";
            
        // Redirect back to the assets page after a delay
        echo "<script>setTimeout(function() { window.location.href = 'viewasset.php?barcode=$barcode'; }, 1000);</script>";
    } else {
        echo "Error:". mysqli_error($conn);
    }
}

    // Close database connection
    mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Asset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php
        // Include the navigation bar
        include 'navbar.php';
    ?>
    <hr>
<div class="container mt-5">
    <h1>Add Asset</h1>
    <hr>
    <form action="" method="post">
        <div class="mb-3">
            <label for="barcode" class="form-label">Barcode:</label>
            <input type="text" id="barcode" name="barcode" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="assetType" class="form-label">Asset Type:</label>
            <select name="assetType" id="assetType" class="form-select" required>
                <option value="">Select Asset Type</option>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                $query = "SELECT DISTINCT assetType FROM asset";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['assetType'] . "'>" . $row['assetType'] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="assetLifeExpectancy" class="form-label">Asset Life Expectancy:</label>
            <input type="text" id="assetLifeExpectancy" name="assetLifeExpectancy" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="installationDate" class="form-label">Installation Date:</label>
            <input type="date" id="installationDate" name="installationDate" class="form-control" required>
        </div>
        <!-- Other input fields for riskscore, locationID, etc. -->
        <div class="mb-3">
            <label for="riskscore" class="form-label">Risk Score:</label>
            <input type="text" id="riskscore" name="riskscore" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="assetCondition" class="form-label">Asset Condition:</label>
            <select name="assetCondition" id="assetCondition" class="form-select" required>
                <option value="">Select Condition Type</option>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                $query = "SELECT DISTINCT assetCondition FROM asset";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['assetCondition'] . "'>" . $row['assetCondition'] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="assetRepairCategory" class="form-label">Asset Repair Category:</label>
            <select name="assetRepairCategory" id="assetRepairCategory" class="form-select" required>
                <option value="">Select Repair Category</option>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                $query = "SELECT DISTINCT assetRepairCategory FROM asset";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['assetRepairCategory'] . "'>" . $row['assetRepairCategory'] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="environmentalRating" class="form-label">Asset Environmental Rating:</label>
            <select name="environmentalRating" id="environmentalRating" class="form-select" required>
                <option value="">Select Environmental Rating</option>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                $query = "SELECT DISTINCT environmentalRating FROM asset";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['environmentalRating'] . "'>" . $row['environmentalRating'] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="impactRating" class="form-label">Asset Impact Rating:</label>
            <select name="impactRating" id="impactRating" class="form-select" required>
                <option value="">Select Impact Rating</option>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                $query = "SELECT DISTINCT impactRating FROM asset";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['impactRating'] . "'>" . $row['impactRating'] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>


        <div class="mb-3">
            <label for="locationID" class="form-label">Location ID:</label>
            <select name="locationID" id="locationID" class="form-select" required>
                <option value="">Select Location</option>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                $query = "SELECT locationID, roomcode FROM location";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['locationID'] . "'>" . $row['roomcode'] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Asset</button>
    </form>
</div>
</body>
</html>
