<?php
// PHP code for data retrieval and database connection
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['role'])) {
    header("location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$building_filter = isset($_GET['building_filter']) ? $_GET['building_filter'] : '';
$type_filter = isset($_GET['type_filter']) ? $_GET['type_filter'] : '';
$risk_filter = isset($_GET['risk']) ? $_GET['risk'] : '';
$condition_filter = isset($_GET['condition_filter']) ? $_GET['condition_filter'] : '';
$repair_filter = isset($_GET['repair_filter']) ? $_GET['repair_filter'] : '';
$roomtype_filter = isset($_GET['roomtype_filter']) ? $_GET['roomtype_filter'] : '';
$manager_filter = isset($_GET['manager_filter']) ? $_GET['manager_filter'] : '';
$site_filter = isset($_GET['site_filter']) ? $_GET['site_filter'] : '';
$impact_rating_filter = isset($_GET['impact_rating_filter']) ? $_GET['impact_rating_filter'] : '';

// Query Database for Asset Statistics

// Total Assets
$totalAssetsQuery = "SELECT COUNT(*) AS total_assets FROM asset AS a ";
$totalAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$totalAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$totalAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$totalAssetsQuery .= "WHERE 1 ";

if (!empty($building_filter)) {
    $totalAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $totalAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $totalAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $totalAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $totalAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $totalAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $totalAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $totalAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $totalAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$totalAssetsResult = mysqli_query($conn, $totalAssetsQuery);
$totalAssets = mysqli_fetch_assoc($totalAssetsResult)['total_assets'];

// critical Assets
$criticalAssetsQuery = "SELECT COUNT(*) AS critical_assets FROM asset AS a ";
$criticalAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$criticalAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$criticalAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$criticalAssetsQuery .= "WHERE a.riskRating = 'CRITICAL' ";

if (!empty($building_filter)) {
    $criticalAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $criticalAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $criticalAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $criticalAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $criticalAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $criticalAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $criticalAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $criticalAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $criticalAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$criticalAssetsResult = mysqli_query($conn, $criticalAssetsQuery);
$criticalAssets = mysqli_fetch_assoc($criticalAssetsResult)['critical_assets'];

// highRisk Assets
$highRiskAssetsQuery = "SELECT COUNT(*) AS highRisk_assets FROM asset AS a ";
$highRiskAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$highRiskAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$highRiskAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$highRiskAssetsQuery .= "WHERE a.riskRating = 'HIGH' ";

if (!empty($building_filter)) {
    $highRiskAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $highRiskAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $highRiskAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $highRiskAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $highRiskAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $highRiskAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $highRiskAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $highRiskAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $highRiskAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$highRiskAssetsResult = mysqli_query($conn, $highRiskAssetsQuery);
$highRiskAssets = mysqli_fetch_assoc($highRiskAssetsResult)['highRisk_assets'];

// mediumRisk Assets
$mediumRiskAssetsQuery = "SELECT COUNT(*) AS mediumRisk_assets FROM asset AS a ";
$mediumRiskAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$mediumRiskAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$mediumRiskAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$mediumRiskAssetsQuery .= "WHERE a.riskRating = 'MEDIUM' ";

if (!empty($building_filter)) {
    $mediumRiskAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $mediumRiskAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $mediumRiskAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $mediumRiskAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $mediumRiskAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $mediumRiskAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $mediumRiskAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $mediumRiskAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $mediumRiskAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$mediumRiskAssetsResult = mysqli_query($conn, $mediumRiskAssetsQuery);
$mediumRiskAssets = mysqli_fetch_assoc($mediumRiskAssetsResult)['mediumRisk_assets'];

// lowRisk Assets
$lowRiskAssetsQuery = "SELECT COUNT(*) AS lowRisk_assets FROM asset AS a ";
$lowRiskAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$lowRiskAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$lowRiskAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$lowRiskAssetsQuery .= "WHERE a.riskRating = 'LOW' ";

if (!empty($building_filter)) {
    $lowRiskAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $lowRiskAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $lowRiskAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $lowRiskAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $lowRiskAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $lowRiskAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $lowRiskAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $lowRiskAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $lowRiskAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$lowRiskAssetsResult = mysqli_query($conn, $lowRiskAssetsQuery);
$lowRiskAssets = mysqli_fetch_assoc($lowRiskAssetsResult)['lowRisk_assets'];

// marylebone Assets
$maryleboneAssetsQuery = "SELECT COUNT(*) AS marylebone_assets FROM asset AS a ";
$maryleboneAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$maryleboneAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$maryleboneAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$maryleboneAssetsQuery .= "WHERE b.site = 'Marylebone Campus' ";

if (!empty($building_filter)) {
    $maryleboneAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $maryleboneAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $maryleboneAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $maryleboneAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $maryleboneAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $maryleboneAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $maryleboneAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $maryleboneAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $maryleboneAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$maryleboneAssetsResult = mysqli_query($conn, $maryleboneAssetsQuery);
$maryleboneAssets = mysqli_fetch_assoc($maryleboneAssetsResult)['marylebone_assets'];

// cavendish Assets
$cavendishAssetsQuery = "SELECT COUNT(*) AS cavendish_assets FROM asset AS a ";
$cavendishAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$cavendishAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$cavendishAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$cavendishAssetsQuery .= "WHERE b.site = 'cavendish Campus' ";

if (!empty($building_filter)) {
    $cavendishAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $cavendishAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $cavendishAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $cavendishAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $cavendishAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $cavendishAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $cavendishAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $cavendishAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $cavendishAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$cavendishAssetsResult = mysqli_query($conn, $cavendishAssetsQuery);
$cavendishAssets = mysqli_fetch_assoc($cavendishAssetsResult)['cavendish_assets'];

// harrow Assets
$harrowAssetsQuery = "SELECT COUNT(*) AS harrow_assets FROM asset AS a ";
$harrowAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$harrowAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$harrowAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$harrowAssetsQuery .= "WHERE b.site = 'harrow Campus' ";

if (!empty($building_filter)) {
    $harrowAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $harrowAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $harrowAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $harrowAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $harrowAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $harrowAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $harrowAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $harrowAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $harrowAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$harrowAssetsResult = mysqli_query($conn, $harrowAssetsQuery);
$harrowAssets = mysqli_fetch_assoc($harrowAssetsResult)['harrow_assets'];

// regent Assets
$regentAssetsQuery = "SELECT COUNT(*) AS regent_assets FROM asset AS a ";
$regentAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$regentAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$regentAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$regentAssetsQuery .= "WHERE b.site = 'Regent Campus' ";

if (!empty($building_filter)) {
    $regentAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $regentAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $regentAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $regentAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $regentAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $regentAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $regentAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $regentAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $regentAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$regentAssetsResult = mysqli_query($conn, $regentAssetsQuery);
$regentAssets = mysqli_fetch_assoc($regentAssetsResult)['regent_assets'];

// su Assets
$suAssetsQuery = "SELECT COUNT(*) AS su_assets FROM asset AS a ";
$suAssetsQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$suAssetsQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$suAssetsQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$suAssetsQuery .= "WHERE b.site = 'University of Westminster Student Union' ";

if (!empty($building_filter)) {
    $suAssetsQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $suAssetsQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $suAssetsQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $suAssetsQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $suAssetsQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $suAssetsQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $suAssetsQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $suAssetsQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $suAssetsQuery .= " AND a.impactRating = '$impact_rating_filter'";
}

$suAssetsResult = mysqli_query($conn, $suAssetsQuery);
$suAssets = mysqli_fetch_assoc($suAssetsResult)['su_assets'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container">
    <hr>
    <h1 class='text-white'>Home Dashboard</h1>
    <hr>
    <div class="row">
        <div class="col-md-2">
            <a href="assets.php" style="text-decoration:none">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Assets</h5>
                        <p class="card-text"><?php echo $totalAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <hr>
    <div class="row">
    <div class="col-md-3">
            <a href="assets.php?risk=CRITICAL" style="text-decoration:none">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Critical Assets</h5>
                        <p class="card-text"><?php echo $criticalAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="assets.php?risk=HIGH" style="text-decoration:none">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">High Risk Assets</h5>
                        <p class="card-text"><?php echo $highRiskAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="assets.php?risk=MEDIUM" style="text-decoration:none">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Medium Risk Assets</h5>
                        <p class="card-text"><?php echo $mediumRiskAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="assets.php?risk=LOW" style="text-decoration:none">
                <div class="card text-white bg-success mb-3" >
                    <div class="card-body">
                        <h5 class="card-title">Low Risk Assets</h5>
                        <p class="card-text"><?php echo $lowRiskAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-2">
            <a href="assets.php" style="text-decoration:none">
                <div class="card text-white bg-secondary mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">All Sites Assets</h5>
                        <p class="card-text"><?php echo $totalAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="assets.php?site_filter=Cavendish+campus" style="text-decoration:none">
                <div class="card text-white bg-secondary mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Cavendish Assets</h5>
                        <p class="card-text"><?php echo $cavendishAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="assets.php?site_filter=harrow+campus" style="text-decoration:none">
                <div class="card text-white bg-secondary mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Harrow Assets</h5>
                        <p class="card-text"><?php echo $harrowAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="assets.php?site_filter=marylebone+campus" style="text-decoration:none">
                <div class="card text-white bg-secondary mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Marylebone Assets</h5>
                        <p class="card-text"><?php echo $maryleboneAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="assets.php?site_filter=regent+campus" style="text-decoration:none">
                <div class="card text-white bg-secondary mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Regent Assets</h5>
                        <p class="card-text"><?php echo $regentAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="assets.php?site_filter=University+of+Westminster+Student+Union" style="text-decoration:none">
                <div class="card text-white bg-secondary mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">University of Westminster Student Union Assets</h5>
                        <p class="card-text"><?php echo $suAssets; ?></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <hr>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
