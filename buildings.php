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
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'buildingName';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$building_filter = isset($_GET['building_filter']) ? $_GET['building_filter'] : '';
$site_filter = isset($_GET['site_filter']) ? $_GET['site_filter'] : '';
$manager_filter = isset($_GET['manager']) ? $_GET['manager'] : '';

$sql = "SELECT * FROM building WHERE 1";

if (!empty($search)) {
    $sql .= " AND (buildingID LIKE '%$search%' OR buildingCode LIKE '%$search%' OR buildingName LIKE '%$search%' OR postCode LIKE '%$search%')";
}

if (!empty($building_filter)) {
    $sql .= " AND buildingName = '$building_filter'";
}

if (!empty($site_filter)) {
    $sql .= " AND site = '$site_filter'";
}

if (!empty($manager_filter)) {
    $sql .= " AND userID = '$manager_filter'";
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
    <title>Buildings</title>
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
    <h1>Buildings</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form action="buildings.php" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Name, ID, Code or Postcode" name="search" value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="buildings.php" method="GET">
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
                        <select name="manager_filter" class="form-control">
                            <option value="">Filter By Manager</option>
                            <?php
                            $manager_query = "SELECT DISTINCT userID FROM building";
                            $manager_result = mysqli_query($conn, $manager_query);
                            while ($manager_row = mysqli_fetch_assoc($manager_result)) {
                                $selected = ($manager_row['userID'] == $manager_filter) ? 'selected' : '';
                                echo "<option value='" . $manager_row['userID'] . "' $selected>" . $manager_row['userID'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="buildings.php" class="btn btn-secondary">Clear Filters</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="bg-dark"><a class="bg-dark text-white" href="buildings.php?sort_by=buildingID&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">ID <?php echo ($sort_by === 'buildingID') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="buildings.php?sort_by=buildingCode&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Code <?php echo ($sort_by === 'buildingCode') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="buildings.php?sort_by=buildingName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Name <?php echo ($sort_by === 'buildingName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="buildings.php?sort_by=site&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Site<?php echo ($sort_by === 'site') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="buildings.php?sort_by=address&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Address <?php echo ($sort_by === 'address') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="buildings.php?sort_by=postCode&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Postcode <?php echo ($sort_by === 'postCode') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="buildings.php?sort_by=userID&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Manager <?php echo ($sort_by === 'userId') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark text-white">View</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Checking for errors
                if (!$queryResult) {
                    echo "<tr><td colspan='8'>Error: " . mysqli_error($conn) . "</td></tr>";
                } elseif (mysqli_num_rows($queryResult) == 0) {
                    echo "<tr><td colspan='8'>No assets found.</td></tr>";
                } else {
                    // Displaying table
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        ?>
                        <tr>
                            <td><?php echo $row['buildingID'] ?></td>
                            <td><?php echo $row['buildingCode'] ?></td>
                            <td><?php echo $row['buildingName'] ?></td>
                            <td><?php echo $row['site'] ?></td>
                            <td><?php echo $row['address'] ?></td>
                            <td><?php echo $row['postCode'] ?></td>
                            <td><?php echo $row['userID'] ?></td>
                            <td><a href="viewbuilding.php?id=<?php echo $row['buildingID'] ?>">View</a></td>
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

