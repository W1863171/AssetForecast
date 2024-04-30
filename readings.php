<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['role'])) {
    header("location: login.php");
    die();
}

// Set up default sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'readingID';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$engineer_filter = isset($_GET['engineer_filter']) ? $_GET['engineer_filter'] : '';

$sql = "SELECT reading.*, user.firstName, user.surname 
        FROM reading 
        JOIN engineer ON reading.userID = engineer.userID 
        JOIN user ON engineer.userID = user.userID 
        WHERE 1";

if (!empty($search)) {
    $sql .= " AND (reading.assetID LIKE '%$search%')";
}

if (!empty($engineer_filter)) {
    $sql .= " AND CONCAT(user.firstName, ' ', user.surname) = '$engineer_filter'";
}

// Add sorting to the SQL query
$sql .= " ORDER BY $sort_by $sort_order";

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Fetching data
$queryResult = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Readings</title>
    <style>
        a {text-decoration:none}
    </style>
</head>
<body class="bg-dark text-white">

<?php
// Include the navigation bar
include 'navbar.php';
?>

<div class="container">
    <hr>
    <h1>Readings</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form action="readings.php" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Asset Barcode" name="search" value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
        
    <div class="row">
        <form action="readings.php" method="GET">
            <div class="col-md-4">
                    <select name="engineer_filter" class="form-control">
                        <option value="">Filter By Engineer</option>
                        <?php
                            $engineer_query = "SELECT DISTINCT CONCAT(firstName, ' ', surname) AS fullName 
                                                FROM user 
                                                WHERE role = 'engineer'";
                            $engineer_result = mysqli_query($conn, $engineer_query);
                            while ($engineer_row = mysqli_fetch_assoc($engineer_result)) {
                                $selected = ($engineer_row['fullName'] == $engineer_filter) ? 'selected' : '';
                                echo "<option value='" . $engineer_row['fullName'] . "' $selected>" . $engineer_row['fullName'] . "</option>";
                            }
                        ?>
                    </select>
               
            </div>
            <hr>
            <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="readings.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        </form>
    </div>
    <hr>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=readingID&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">ID <?php echo ($sort_by === 'readingID') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=readingType&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Type <?php echo ($sort_by === 'readingType') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=readingValue&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Value <?php echo ($sort_by === 'readingValue') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=readingDate&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Date <?php echo ($sort_by === 'readingDate') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=newAssetCondition&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">New Asset Condition <?php echo ($sort_by === 'newAssetCondition') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=newRepairCategory&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">New Repair Category <?php echo ($sort_by === 'newRepairCategory') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=userID&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Engineer <?php echo ($sort_by === 'userId') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="readings.php?sort_by=assetID&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Asset Barcode <?php echo ($sort_by === 'locationID') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Checking for errors
                if (!$queryResult) {
                    echo "<tr><td colspan='8'>Error: " . mysqli_error($conn) . "</td></tr>";
                } elseif (mysqli_num_rows($queryResult) == 0) {
                    echo "<tr><td colspan='8'>No readings found.</td></tr>";
                } else {
                    // Displaying table
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        ?>
                        <tr>
                            <td><?php echo $row['readingID'] ?></td>
                            <td><?php echo $row['readingType'] ?></td>
                            <td><?php echo $row['readingValue'] ?></td>
                            <td><?php echo $row['readingDate'] ?></td>
                            <td><?php echo $row['newAssetCondition'] ?></td>
                            <td><?php echo $row['newRepairCategory'] ?></td>
                            <td><?php echo $row['firstName'] . ' ' . $row['surname'] ?></td>
                            <td><?php echo $row['assetID'] ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
