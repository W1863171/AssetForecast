<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['role'])) {
  header("location: login.php");
  exit();
}

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'facilities manager') {
    header("location: home.php");
    exit();
}

// Get the asset barcode from the URL
$barcode = $_GET['barcode'];

// Database connection
$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch asset data from the database
function fetch_asset_data($conn, $barcode, $attribute)
{
    $query = "SELECT $attribute FROM asset WHERE barcode = '$barcode'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row[$attribute];
    }
    return ''; // Return empty string if attribute not found
}

// Fetch current asset data
$current_condition = fetch_asset_data($conn, $barcode, 'assetCondition');
$current_repair_category = fetch_asset_data($conn, $barcode, 'assetRepairCategory');
$current_environmental_rating = fetch_asset_data($conn, $barcode, 'environmentalRating');
$current_impact_rating = fetch_asset_data($conn, $barcode, 'impactRating');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $asset_condition = isset($_POST['asset_condition']) ? $_POST['asset_condition'] : '';
    $asset_repair_category = isset($_POST['asset_repair_category']) ? $_POST['asset_repair_category'] : '';
    $environmental_rating = isset($_POST['environmental_rating']) ? $_POST['environmental_rating'] : '';
    $impact_rating = isset($_POST['impact_rating']) ? $_POST['impact_rating'] : '';

    // SQL update statement
    $sql = "UPDATE asset 
            SET assetCondition='$asset_condition', 
                assetRepairCategory='$asset_repair_category', 
                environmentalRating='$environmental_rating', 
                impactRating='$impact_rating' 
            WHERE barcode='$barcode'";

    // Execute the update query
    if (mysqli_query($conn, $sql)) {
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
mysqli_close($conn)
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset Information</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
<?php
// Include the navigation bar
include 'navbar.php';
?>

    <div class="container mt-5 ">
        <hr>
        <h1>Edit Asset Information - Barcode: <?php echo $barcode; ?></h1>
        <hr>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?barcode=' . $barcode; ?>">
            <!-- Asset Condition Dropdown -->
            <div class="mb-3">
                <label for="asset_condition" class="form-label">Asset Condition</label>
                <select class="form-select" id="asset_condition" name="asset_condition" >
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

            <!-- Asset Repair Category Dropdown -->
            <div class="mb-3">
                <label for="asset_repair_category" class="form-label">Asset Repair Category</label>
                <select class="form-select" id="asset_repair_category" name="asset_repair_category">
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

            <!-- Environmental Rating Dropdown -->
            <div class="mb-3">
                <label for="environmental_rating" class="form-label">Environmental Rating</label>
                <select class="form-select" id="environmental_rating" name="environmental_rating">
                    <?php
                    echo "<option value=''</option>";
                    // Populate environmental rating dropdown
                    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                    $query = "SELECT DISTINCT environmentalRating FROM asset ORDER BY environmentalRating";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['environmentalRating'] . "'>" . $row['environmentalRating'] . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                </select>
            </div>

            <!-- Impact Rating Dropdown -->
            <div class="mb-3">
                <label for="impact_rating" class="form-label">Impact Rating</label>
                <select class="form-select" id="impact_rating" name="impact_rating">
                    <?php
                    echo "<option value=''</option>";
                    // Populate impact rating dropdown
                    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");
                    $query = "SELECT DISTINCT impactRating FROM asset ORDER BY impactRating";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['impactRating'] . "'>" . $row['impactRating'] . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Save Changes & Calulcate New Risk Score</button>
        </form>
        
</body>
</html>

