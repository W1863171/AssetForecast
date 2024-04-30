<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['role'])) {
  header("location: login.php");
  exit();
}

// Set up default sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'barcode';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

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

// Pagination settings
$limit = 1000; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

$sql_total = "SELECT COUNT(*) AS total FROM asset a
                INNER JOIN location l ON a.locationID = l.locationID
                INNER JOIN building b ON l.buildingID = b.buildingID
                INNER JOIN facilities_manager fm ON b.userID = fm.userID
                INNER JOIN user u ON fm.userID = u.userID";

$sql = "SELECT a.barcode, a.assetType, a.assetLifeExpectancy, a.installationDate, 
               TIMESTAMPDIFF(YEAR, a.installationDate, CURDATE()) AS assetAge, 
               a.riskScore, 
               CASE
                   WHEN a.riskScore BETWEEN 0 AND 30 THEN 'CRITICAL'
                   WHEN a.riskScore BETWEEN 31 AND 50 THEN 'HIGH'
                   WHEN a.riskScore BETWEEN 51 AND 75 THEN 'MEDIUM'
                   WHEN a.riskScore BETWEEN 76 AND 100 THEN 'LOW'
                   ELSE 'UNKNOWN'
               END AS riskRating, 
               a.assetCondition, a.assetRepairCategory, a.environmentalRating, a.impactRating, 
               l.roomCode, 
               b.site, b.buildingName, 
               CONCAT(u.firstName, ' ', u.surname) AS managerName
        FROM asset a
        INNER JOIN location l ON a.locationID = l.locationID
        INNER JOIN building b ON l.buildingID = b.buildingID
        INNER JOIN facilities_manager fm ON b.userID = fm.userID
        INNER JOIN user u ON fm.userID = u.userID";

if (!empty($search)) {
    $sql .= " WHERE a.barcode LIKE '%$search%'";
    $sql_total .= " WHERE a.barcode LIKE '%$search%'";
}

if (!empty($building_filter)) {
    $sql .= " AND b.buildingName = '$building_filter'";
    $sql_total .= " AND b.buildingName = '$building_filter'";
}

if (!empty($type_filter)) {
    $sql .= " AND a.assetType = '$type_filter'";
    $sql_total .= " AND a.assetType = '$type_filter'";
}

if (!empty($risk_filter)) {
    $sql .= " AND a.riskRating = '$risk_filter'";
    $sql_total .= " AND a.riskRating = '$risk_filter'";
}

if (!empty($condition_filter)) {
    $sql .= " AND a.assetCondition = '$condition_filter'";
    $sql_total .= " AND a.assetCondition = '$condition_filter'";
}

if (!empty($repair_filter)) {
    $sql .= " AND a.assetRepairCategory = '$repair_filter'";
    $sql_total .= " AND a.assetRepairCategory = '$repair_filter'";
}

if (!empty($roomtype_filter)) {
    $sql .= " AND l.roomType = '$roomtype_filter'";
    $sql_total .= " AND l.roomType = '$roomtype_filter'";
}

if (!empty($manager_filter)) {
    // $manager_filter contains the concatenated first name and surname
    $sql .= " AND CONCAT(u.firstName, ' ', u.surname) = '$manager_filter'";
    $sql_total .= " AND CONCAT(u.firstName, ' ', u.surname) = '$manager_filter'";
}

if (!empty($site_filter)) {
    $sql .= " AND b.site = '$site_filter'";
    $sql_total .= " AND b.site = '$site_filter'";
}

if (!empty($impact_rating_filter)) {
    $sql .= " AND a.impactRating = '$impact_rating_filter'";
    $sql_total .= " AND a.impactRating = '$impact_rating_filter'";
}

// Add sorting to the SQL query
$sql .= " ORDER BY $sort_by $sort_order";
$sql .= " LIMIT $limit OFFSET $offset";

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Fetching data
$queryResult = mysqli_query($conn, $sql);
$totalResult = mysqli_query($conn, $sql_total);
$totalAssets = mysqli_fetch_assoc($totalResult)['total'];

// Calculate total pages
$totalPages = ceil($totalAssets / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        a {text-decoration:none}
    </style>
    <title>Assets</title>
</head>
<body class="bg-dark text-white">
<?php
// Include the navigation bar
include 'navbar.php';
?>
<div class="container">
    <hr>
    <h1>Assets</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form action="assets.php" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Barcode" name="search" value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="assets.php" method="GET">
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
                </div>
                <div class="row mt-2">
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
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-danger">Apply Filters</button>
                        <a href="assets.php" class="btn btn-secondary">Clear Filters</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <button type="submit" class="btn btn-danger" id="downloadButton">Download Assets</button>     
        </div>
    </div>
    <div class="table-responsive">
        <table id='assetTable' class="table table-hover">
            <thead class="thead-dark">
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=barcode&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Barcode <?php echo ($sort_by === 'barcode') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=assetType&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Asset Type <?php echo ($sort_by === 'assetType') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=assetAge&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Asset Age <?php echo ($sort_by === 'assetAge') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=installationDate&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Installation Date <?php echo ($sort_by === 'installationDate') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=riskScore&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Risk Score <?php echo ($sort_by === 'riskScore') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=riskRating&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Risk Rating <?php echo ($sort_by === 'riskRating') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=assetCondition&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Asset Condition <?php echo ($sort_by === 'assetCondition') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=assetRepairCategory&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Asset Repair Category <?php echo ($sort_by === 'assetRepairCategory') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=environmentalRating&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Environmental Rating <?php echo ($sort_by === 'environmentalRating') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=impactRating&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Impact Rating <?php echo ($sort_by === 'impactRating') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=roomCode&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Room Code <?php echo ($sort_by === 'roomCode') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=site&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Site <?php echo ($sort_by === 'site') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=buildingName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Building Name <?php echo ($sort_by === 'buildingName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="assets.php?sort_by=managerName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Manager Name <?php echo ($sort_by === 'managerName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark text-white">View</th>
            </thead>
            <tbody>
                <?php
                // Checking for errors
                if (!$queryResult) {
                    echo "<tr><td colspan='14'>Error: " . mysqli_error($conn) . "</td></tr>";
                } elseif (mysqli_num_rows($queryResult) == 0) {
                    echo "<tr><td colspan='14'>No assets found.</td></tr>";
                } else {
                    // Displaying table
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        ?>
                        <tr>
                            <td><?php echo $row['barcode'] ?></td>
                            <td><?php echo $row['assetType'] ?></td>
                            <td><?php echo $row['assetAge'] ?></td>
                            <td><?php echo $row['installationDate'] ?></td>
                            <td><?php echo $row['riskScore'] ?></td>
                            <td><?php echo $row['riskRating'] ?></td>
                            <td><?php echo $row['assetCondition'] ?></td>
                            <td><?php echo $row['assetRepairCategory'] ?></td>
                            <td><?php echo $row['environmentalRating'] ?></td>
                            <td><?php echo $row['impactRating'] ?></td>
                            <td><?php echo $row['roomCode'] ?></td>
                            <td><?php echo $row['site'] ?></td>
                            <td><?php echo $row['buildingName'] ?></td>
                            <td><?php echo $row['managerName'] ?></td>
                            <td><a href="viewasset.php?barcode=<?php echo $row['barcode'] ?>">View</a></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="assets.php?page=<?php echo ($page - 1); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="assets.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="assets.php?page=<?php echo ($page + 1); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<script>
    document.getElementById("downloadButton").addEventListener("click", function() {
        // Get the table
        var table = document.getElementById("assetTable");

        // Create an empty string to store the CSV data
        var csv = [];

        // Loop through each row in the table
        for (var i = 0; i < table.rows.length; i++) {
            var row = table.rows[i];
            var rowData = [];

            // Loop through each cell in the row
            for (var j = 0; j < row.cells.length; j++) {
                // Push the cell's text content to the row data array
                rowData.push(row.cells[j].innerText);
            }

            // Combine the row data into a CSV line and push it to the CSV array
            csv.push(rowData.join(","));
        }

        // Combine the CSV lines into a single CSV string
        var csvString = csv.join("\n");

        // Create a blob from the CSV string
        var blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });

        // Create a link element to download the CSV file
        var link = document.createElement("a");
        if (link.download !== undefined) {
            // Set the download attribute and the href attribute
            link.setAttribute("href", URL.createObjectURL(blob));
            link.setAttribute("download", "assets.csv");
            link.style.visibility = "hidden";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
