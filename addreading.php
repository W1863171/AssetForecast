<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['role'])) {
    header("location: login.php");
    die();
}

// Restrict access to engineers
$allowed_roles = ['engineer'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("location: home.php");
    die();
}

// Get the barcode from the URL parameter
$barcode = isset($_GET['barcode']) ? $_GET['barcode'] : '';
if (empty($barcode)) {
    // Redirect if barcode is not provided
    header("location: home.php");
    die();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $readingType = $_POST['readingType'];
    $readingValue = $_POST['readingValue'];
    $readingDate = $_POST['readingDate'];
    $newAssetCondition = $_POST['asset_condition'];
    $newAssetRepairCategory = $_POST['asset_repair_category'];
    $userID = $_SESSION['userID']; 

    // Insert into reading table
    $insertReadingQuery = "INSERT INTO reading (readingType, readingValue, readingDate, newAssetCondition, newRepairCategory, userID, assetID) 
                           VALUES ('$readingType', '$readingValue', '$readingDate', '$newAssetCondition', '$newAssetRepairCategory', '$userID', '$barcode')";

    if (mysqli_query($conn, $insertReadingQuery)) {
        // Update asset table with new repair category and condition
        $updateAssetQuery = "UPDATE asset SET assetCondition = '$newAssetCondition', assetRepairCategory = '$newAssetRepairCategory' WHERE barcode = '$barcode'";
        mysqli_query($conn, $updateAssetQuery);

        // Invoke the Python script to recalculate the risk score and capture the message
        $command = '../AssetForecast/python ../AssetForecast/predictriskscore.py ' . escapeshellarg($barcode);
        $output = shell_exec($command);
        
        // Display new risk score
        echo "$output";
        
        // Redirect back to the assets page after a delay
        echo "<script>setTimeout(function() { window.location.href = 'viewasset.php?barcode=$barcode'; }, 1000);</script>";
    } else {
        // Error handling
        echo "Error: " . $insertReadingQuery . "<br>" . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Reading</title>
</head>
<body class="bg-dark text-white">

    <?php
    // Include the navigation bar
    include 'navbar.php';
    ?>

    <div class="container">
        <hr>
        <h1>Add Reading - Asset Barcode: <?php echo $barcode; ?></h1>
        <hr>
        <!-- Reading form -->
        <form action="addreading.php?barcode=<?php echo $barcode; ?>" method="POST">
            <!-- Hidden input field to store the barcode -->
            <input type="hidden" name="barcode" value="<?php echo $barcode; ?>">
            <div class="mb-3">
                <label for="readingType" class="form-label">Reading Type</label>
                <input type="text" class="form-control" id="readingType" name="readingType" required>
            </div>
            <div class="mb-3">
                <label for="readingValue" class="form-label">Reading Value</label>
                <input type="number" class="form-control" id="readingValue" name="readingValue" required>
            </div>
            <div class="mb-3">
                <label for="readingDate" class="form-label">Reading Date</label>
                <input type="date" class="form-control" id="readingDate" name="readingDate" required>
            </div>
            <!-- New Asset Condition Dropdown -->
            <div class="mb-3">
                <label for="asset_condition" class="form-label">New Asset Condition</label>
                <select class="form-select" id="asset_condition" name="asset_condition" required>
                    <?php
                    echo "<option value=''</option>";
                    // Populate asset condition dropdown
                    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                    $query = "SELECT DISTINCT assetCondition FROM asset ORDER BY assetCondition";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['assetCondition'] . "'>" . $row['assetCondition'] . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                </select>
            </div>
            <!-- New Asset Repair Category Dropdown -->
            <div class="mb-3">
                <label for="asset_repair_category" class="form-label">New Asset Repair Category</label>
                <select class="form-select" id="asset_repair_category" name="asset_repair_category" required>
                    <?php
                    echo "<option value=''</option>";
                    // Populate asset repair category dropdown
                    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                    $query = "SELECT DISTINCT assetRepairCategory FROM asset ORDER BY assetRepairCategory";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['assetRepairCategory'] . "'>" . $row['assetRepairCategory'] . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
