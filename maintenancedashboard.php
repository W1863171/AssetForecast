<?php
// PHP code for data retrieval and database connection
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
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
$taskstatus_filter = isset($_GET['taskstatus_filter']) ? $_GET['taskstatus_filter'] : '';

// Query Database for Asset Statistics

// Total Maintenance
$totalMaintenanceQuery = "SELECT COUNT(*) AS total_Maintenance FROM maintenance_task AS mt ";
$totalMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";;
$totalMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$totalMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$totalMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$totalMaintenanceQuery .= "WHERE 1 ";

if (!empty($building_filter)) {
    $totalMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $totalMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $totalMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $totalMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $totalMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $totalMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $totalMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $totalMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $totalMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $totalMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$totalMaintenanceResult = mysqli_query($conn, $totalMaintenanceQuery);
$totalMaintenance = mysqli_fetch_assoc($totalMaintenanceResult)['total_Maintenance'];

// marylebone Maintenance
$maryleboneMaintenanceQuery = "SELECT COUNT(*) AS marylebone_Maintenance FROM maintenance_task AS mt  ";
$maryleboneMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";;
$maryleboneMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$maryleboneMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$maryleboneMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$maryleboneMaintenanceQuery .= "WHERE b.site = 'Marylebone Campus' ";

if (!empty($building_filter)) {
    $maryleboneMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $maryleboneMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $maryleboneMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $maryleboneMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $maryleboneMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $maryleboneMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $maryleboneMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $maryleboneMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $maryleboneMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $maryleboneMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$maryleboneMaintenanceResult = mysqli_query($conn, $maryleboneMaintenanceQuery);
$maryleboneMaintenance = mysqli_fetch_assoc($maryleboneMaintenanceResult)['marylebone_Maintenance'];

// cavendish Maintenance
$cavendishMaintenanceQuery = "SELECT COUNT(*) AS cavendish_Maintenance FROM maintenance_task AS mt  ";
$cavendishMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";;
$cavendishMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$cavendishMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$cavendishMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$cavendishMaintenanceQuery .= "WHERE b.site = 'cavendish Campus' ";

if (!empty($building_filter)) {
    $cavendishMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $cavendishMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $cavendishMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $cavendishMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $cavendishMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $cavendishMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $cavendishMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $cavendishMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $cavendishMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $cavendishMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$cavendishMaintenanceResult = mysqli_query($conn, $cavendishMaintenanceQuery);
$cavendishMaintenance = mysqli_fetch_assoc($cavendishMaintenanceResult)['cavendish_Maintenance'];

// harrow Maintenance
$harrowMaintenanceQuery = "SELECT COUNT(*) AS harrow_Maintenance FROM maintenance_task AS mt  ";
$harrowMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";;
$harrowMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$harrowMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$harrowMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$harrowMaintenanceQuery .= "WHERE b.site = 'harrow Campus' ";

if (!empty($building_filter)) {
    $harrowMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $harrowMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $harrowMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $harrowMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $harrowMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $harrowMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $harrowMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $harrowMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $harrowMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";

if (!empty($taskstatus_filter)) {
    $harrowMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}}

$harrowMaintenanceResult = mysqli_query($conn, $harrowMaintenanceQuery);
$harrowMaintenance = mysqli_fetch_assoc($harrowMaintenanceResult)['harrow_Maintenance'];

// regent Maintenance
$regentMaintenanceQuery = "SELECT COUNT(*) AS regent_Maintenance FROM maintenance_task AS mt ";
$regentMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";;
$regentMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$regentMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$regentMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$regentMaintenanceQuery .= "WHERE b.site = 'Regent Campus' ";

if (!empty($building_filter)) {
    $regentMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $regentMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $regentMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $regentMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $regentMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $regentMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $regentMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $regentMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $regentMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $regentMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$regentMaintenanceResult = mysqli_query($conn, $regentMaintenanceQuery);
$regentMaintenance = mysqli_fetch_assoc($regentMaintenanceResult)['regent_Maintenance'];

// su Maintenance
$suMaintenanceQuery = "SELECT COUNT(*) AS su_Maintenance FROM maintenance_task AS mt ";
$suMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";
$suMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$suMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$suMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$suMaintenanceQuery .= "WHERE b.site = 'University of Westminster Student Union' ";

if (!empty($building_filter)) {
    $suMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $suMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $suMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $suMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $suMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $suMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $suMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $suMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $suMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $suMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$suMaintenanceResult = mysqli_query($conn, $suMaintenanceQuery);
$suMaintenance = mysqli_fetch_assoc($suMaintenanceResult)['su_Maintenance'];

// notApproved Maintenance
$notApprovedMaintenanceQuery = "SELECT COUNT(*) AS notApproved_Maintenance FROM maintenance_task AS mt ";
$notApprovedMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";
$notApprovedMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$notApprovedMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$notApprovedMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$notApprovedMaintenanceQuery .= "WHERE mt.taskStatus = 'Not Approved' ";

if (!empty($building_filter)) {
    $notApprovedMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $notApprovedMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $notApprovedMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $notApprovedMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $notApprovedMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $notApprovedMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $notApprovedMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $notApprovedMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $notApprovedMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $notApprovedMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$notApprovedMaintenanceResult = mysqli_query($conn, $notApprovedMaintenanceQuery);
$notApprovedMaintenance = mysqli_fetch_assoc($notApprovedMaintenanceResult)['notApproved_Maintenance'];

// released Maintenance
$releasedMaintenanceQuery = "SELECT COUNT(*) AS released_Maintenance FROM maintenance_task AS mt ";
$releasedMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";
$releasedMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$releasedMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$releasedMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$releasedMaintenanceQuery .= "WHERE mt.taskStatus = 'Released' ";

if (!empty($building_filter)) {
    $releasedMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $releasedMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $releasedMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $releasedMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $releasedMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $releasedMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $releasedMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $releasedMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $releasedMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $releasedMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$releasedMaintenanceResult = mysqli_query($conn, $releasedMaintenanceQuery);
$releasedMaintenance = mysqli_fetch_assoc($releasedMaintenanceResult)['released_Maintenance'];

// started Maintenance
$startedMaintenanceQuery = "SELECT COUNT(*) AS started_Maintenance FROM maintenance_task AS mt ";
$startedMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";
$startedMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$startedMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$startedMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$startedMaintenanceQuery .= "WHERE mt.taskStatus = 'Started' ";

if (!empty($building_filter)) {
    $startedMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $startedMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $startedMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $startedMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $startedMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $startedMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $startedMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $startedMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $startedMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $startedMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$startedMaintenanceResult = mysqli_query($conn, $startedMaintenanceQuery);
$startedMaintenance = mysqli_fetch_assoc($startedMaintenanceResult)['started_Maintenance'];

// workDone Maintenance
$workDoneMaintenanceQuery = "SELECT COUNT(*) AS workDone_Maintenance FROM maintenance_task AS mt ";
$workDoneMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";
$workDoneMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$workDoneMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$workDoneMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$workDoneMaintenanceQuery .= "WHERE mt.taskStatus = 'WorkDone' ";

if (!empty($building_filter)) {
    $workDoneMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $workDoneMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $workDoneMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $workDoneMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $workDoneMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $workDoneMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $workDoneMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $workDoneMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $workDoneMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $workDoneMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$workDoneMaintenanceResult = mysqli_query($conn, $workDoneMaintenanceQuery);
$workDoneMaintenance = mysqli_fetch_assoc($workDoneMaintenanceResult)['workDone_Maintenance'];

// assigned Maintenance
$assignedMaintenanceQuery = "SELECT COUNT(*) AS assigned_Maintenance FROM maintenance_task AS mt ";
$assignedMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";
$assignedMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$assignedMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$assignedMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$assignedMaintenanceQuery .= "WHERE mt.taskStatus = 'Assigned' ";

if (!empty($building_filter)) {
    $assignedMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $assignedMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $assignedMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $assignedMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $assignedMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $assignedMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $assignedMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $assignedMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $assignedMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $assignedMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$assignedMaintenanceResult = mysqli_query($conn, $assignedMaintenanceQuery);
$assignedMaintenance = mysqli_fetch_assoc($assignedMaintenanceResult)['assigned_Maintenance'];

// finished Maintenance
$finishedMaintenanceQuery = "SELECT COUNT(*) AS finished_Maintenance FROM maintenance_task AS mt ";
$finishedMaintenanceQuery .= "INNER JOIN asset AS a ON mt.barcode = a.barcode ";
$finishedMaintenanceQuery .= "INNER JOIN location AS l ON a.locationID = l.locationID ";
$finishedMaintenanceQuery .= "INNER JOIN building AS b ON l.buildingID = b.buildingID ";
$finishedMaintenanceQuery .= "LEFT JOIN user AS u ON b.userID = u.userID ";
$finishedMaintenanceQuery .= "WHERE mt.taskStatus = 'Finished' ";

if (!empty($building_filter)) {
    $finishedMaintenanceQuery .= " AND b.buildingName = '$building_filter'";
}
if (!empty($type_filter)) {
    $finishedMaintenanceQuery .= " AND a.assetType = '$type_filter'";
}
if (!empty($risk_filter)) {
    $finishedMaintenanceQuery .= " AND a.riskRating = '$risk_filter'";
}
if (!empty($condition_filter)) {
    $finishedMaintenanceQuery .= " AND a.assetCondition = '$condition_filter'";
}
if (!empty($repair_filter)) {
    $finishedMaintenanceQuery .= " AND a.assetRepairCategory = '$repair_filter'";
}
if (!empty($roomtype_filter)) {
    $finishedMaintenanceQuery .= " AND l.roomType = '$roomtype_filter'";
}
if (!empty($manager_filter)) {
    // Split manager_filter into first name and last name
    list($managerFirstName, $managerLastName) = explode(" ", $manager_filter);
    $finishedMaintenanceQuery .= " AND u.firstName = '$managerFirstName' AND u.lastName = '$managerLastName'";
}
if (!empty($site_filter)) {
    $finishedMaintenanceQuery .= " AND b.site = '$site_filter'";
}
if (!empty($impact_rating_filter)) {
    $finishedMaintenanceQuery .= " AND a.impactRating = '$impact_rating_filter'";
}
if (!empty($taskstatus_filter)) {
    $finishedMaintenanceQuery .= " AND mt.taskStatus = '$taskstatus_filter'";
}

$finishedMaintenanceResult = mysqli_query($conn, $finishedMaintenanceQuery);
$finishedMaintenance = mysqli_fetch_assoc($finishedMaintenanceResult)['finished_Maintenance'];


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

<?php
// Include the navigation bar
include 'navbar.php';
?>

<div class="container">
    <hr>
    <h1>Maintenance Dashboard</h1>
    <hr>
     <div class="row">
        <div class="col-md-12">
            <form action="maintenancedashboard.php" method="GET">
                 <div class="row">
                    <div class="col-md-3">
                        <select name="building_filter" class="form-control">
                            <option value="">Filter By Building</option>
                            <?php
                            $building_query = "SELECT DISTINCT buildingName FROM building ORDER BY buildingName";
                            $building_result = mysqli_query($conn, $building_query);
                            while ($building_row = mysqli_fetch_assoc($building_result)) {
                                $selected = ($building_row['buildingName'] == $building_filter) ? 'selected' : '';
                                echo "<option value='" . $building_row['buildingName'] . "' $selected>" . $building_row['buildingName'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type_filter" class="form-control">
                            <option value="">Filter By Type</option>
                            <?php
                            $type_query = "SELECT DISTINCT assetType FROM asset ORDER BY assetType";
                            $type_result = mysqli_query($conn, $type_query);
                            while ($type_row = mysqli_fetch_assoc($type_result)) {
                                $selected = ($type_row['assetType'] == $type_filter) ? 'selected' : '';
                                echo "<option value='" . $type_row['assetType'] . "' $selected>" . $type_row['assetType'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="risk" class="form-control">
                            <option value="">Filter By Risk</option>
                            <option value="HIGH" <?php echo ($risk_filter == 'HIGH') ? 'selected' : ''; ?>>High</option>
                            <option value="MEDIUM" <?php echo ($risk_filter == 'MEDIUM') ? 'selected' : ''; ?>>Medium</option>
                            <option value="LOW" <?php echo ($risk_filter == 'LOW') ? 'selected' : ''; ?>>Low</option>
                            <option value="CRITICAL" <?php echo ($risk_filter == 'CRITICAL') ? 'selected' : ''; ?>>Critical</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="condition_filter" class="form-control">
                            <option value="">Filter By Condition</option>
                            <?php
                            $condition_query = "SELECT DISTINCT assetCondition FROM asset ORDER BY assetCondition";
                            $condition_result = mysqli_query($conn, $condition_query);
                            while ($condition_row = mysqli_fetch_assoc($condition_result)) {
                                $selected = ($condition_row['assetCondition'] == $condition_filter) ? 'selected' : '';
                                echo "<option value='" . $condition_row['assetCondition'] . "' $selected>" . $condition_row['assetCondition'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <select name="repair_filter" class="form-control">
                            <option value="">Filter By Repair Category</option>
                            <?php
                            $repair_query = "SELECT DISTINCT assetRepairCategory FROM asset ORDER BY assetRepairCategory";
                            $repair_result = mysqli_query($conn, $repair_query);
                            while ($repair_row = mysqli_fetch_assoc($repair_result)) {
                                $selected = ($repair_row['assetRepairCategory'] == $repair_filter) ? 'selected' : '';
                                echo "<option value='" . $repair_row['assetRepairCategory'] . "' $selected>" . $repair_row['assetRepairCategory'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="impact_rating_filter" class="form-control">
                            <option value="">Filter By Impact Rating</option>
                            <?php
                            $impact_query = "SELECT DISTINCT impactRating FROM asset ORDER BY impactRating";
                            $impact_result = mysqli_query($conn, $impact_query);
                            while ($impact_row = mysqli_fetch_assoc($impact_result)) {
                                $selected = ($impact_row['impactRating'] == $impact_rating_filter) ? 'selected' : '';
                                echo "<option value='" . $impact_row['impactRating'] . "' $selected>" . $impact_row['impactRating'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="site_filter" class="form-control">
                            <option value="">Filter By Site</option>
                            <?php
                            $site_query = "SELECT DISTINCT site FROM building ORDER BY site";
                            $site_result = mysqli_query($conn, $site_query);
                            while ($site_row = mysqli_fetch_assoc($site_result)) {
                                $selected = ($site_row['site'] == $site_filter) ? 'selected' : '';
                                echo "<option value='" . $site_row['site'] . "' $selected>" . $site_row['site'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="taskstatus_filter" class="form-control">
                            <option value="">Filter By Task Status</option>
                            <?php
                            $taskstatus_query = "SELECT DISTINCT taskStatus FROM maintenance_task ORDER BY taskStatus";
                            $taskstatus_result = mysqli_query($conn, $taskstatus_query);
                            while ($taskstatus_row = mysqli_fetch_assoc($taskstatus_result)) {
                                $selected = ($taskstatus_row['taskStatus'] == $taskstatus_filter) ? 'selected' : '';
                                echo "<option value='" . $taskstatus_row['taskStatus'] . "' $selected>" . $taskstatus_row['taskStatus'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-info">Apply Filters</button>
                        <a href="maintenancedashboard.php" class="btn btn-outline-danger">Clear Filters</a>
                    </div>
                </div>
                         
            </form>
        </div>
    </div>
    <hr>
    <div class="row-md-12">
        <div class="col-md-2">
            <a href="maintenancetasks.php" style="text-decoration:none">
                <div class="card text-white bg-primary mb-3" >
                    <div class="card-body">
                        <h5 class="card-title">Total Maintenance</h5>
                        <p class="card-text"><?php echo $totalMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-2">
            <a href="maintenancetasks.php?taskstatus_filter=Not+Approved" style="text-decoration:none">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Not Approved Maintenance Tasks</h5>
                        <p class="card-text"><?php echo $notApprovedMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?taskstatus_filter=Released" style="text-decoration:none">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Released Maintenance Tasks</h5>
                        <p class="card-text"><?php echo $releasedMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?taskstatus_filter=Assigned" style="text-decoration:none">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Assigned Maintenance Tasks</h5>
                        <p class="card-text"><?php echo $assignedMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?taskstatus_filter=Started" style="text-decoration:none">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Started Maintenance Tasks</h5>
                        <p class="card-text"><?php echo $startedMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?taskstatus_filter=WorkDone" style="text-decoration:none">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">WorkDone Maintenance Tasks</h5>
                        <p class="card-text"><?php echo $workDoneMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?taskstatus_filter=Finished" style="text-decoration:none">
                <div class="card text-white bg-dark mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Finished Maintenance Tasks</h5>
                        <p class="card-text"><?php echo $finishedMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-2">
            <a href="Maintenance.php" style="text-decoration:none">
                <div class="card text-white bg-dark mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">All Sites Maintenance</h5>
                        <p class="card-text"><?php echo $totalMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?site_filter=Cavendish+campus" style="text-decoration:none">
                <div class="card text-white bg-dark mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Cavendish Maintenance</h5>
                        <p class="card-text"><?php echo $cavendishMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?site_filter=harrow+campus" style="text-decoration:none">
                <div class="card text-white bg-dark mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Harrow Maintenance</h5>
                        <p class="card-text"><?php echo $harrowMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?site_filter=marylebone+campus" style="text-decoration:none">
                <div class="card text-white bg-dark mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Marylebone Maintenance</h5>
                        <p class="card-text"><?php echo $maryleboneMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?site_filter=regent+campus" style="text-decoration:none">
                <div class="card text-white bg-dark mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">Regent Maintenance</h5>
                        <p class="card-text"><?php echo $regentMaintenance; ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="maintenancetasks.php?site_filter=University+of+Westminster+Student+Union" style="text-decoration:none">
                <div class="card text-white bg-dark mb-3" style="height: 155px;">
                    <div class="card-body">
                        <h5 class="card-title">University of Westminster Student Union Maintenance</h5>
                        <p class="card-text"><?php echo $suMaintenance; ?></p>
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
