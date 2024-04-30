<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'locationID';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$type_filter = isset($_GET['type_filter']) ? $_GET['type_filter'] : '';
$building_filter = isset($_GET['building_filter']) ? $_GET['building_filter'] : '';

$sql = "SELECT location.*, building.buildingName FROM location 
        LEFT JOIN building ON location.buildingID = building.buildingID 
        WHERE 1";

if (!empty($search)) {
    $sql .= " AND roomCode LIKE '%$search%'";
}

if (!empty($type_filter)) {
    $sql .= " AND roomType = '$type_filter'";
}

if (!empty($building_filter)) {
    $sql .= " AND building.buildingName = '$building_filter'";
}

$sql .= " ORDER BY $sort_by $sort_order";

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

$queryResult = mysqli_query($conn, $sql);

// Fetch distinct room types
$distinct_type_query = "SELECT DISTINCT roomType FROM location ORDER BY roomType";
$distinct_type_result = mysqli_query($conn, $distinct_type_query);

$distinct_types = array();
while ($row = mysqli_fetch_assoc($distinct_type_result)) {
    $distinct_types[] = $row['roomType'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Locations</title>
</head>
<body class="bg-dark text-white">

<?php
// Include the navigation bar
include 'adminnavbar.php';
?>
<div class="container">
    <hr>
    <h1>Locations</h1>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <form action="locations.php" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Room Code" name="search" value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>
        <div class="col-md-8">
            <form action="locations.php" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <select name="type_filter" class="form-control">
                            <option value="">Filter By Type</option>
                            <?php foreach ($distinct_types as $type) : ?>
                                <option value="<?php echo $type; ?>" <?php echo ($type_filter == $type) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="locations.php" class="btn btn-secondary">Clear Filters</a>
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
                    <th class="bg-dark text-white">ID</th>
                    <th class="bg-dark text-white">Room Code</th>
                    <th class="bg-dark text-white">Room Type</th>
                    <th class="bg-dark text-white">Floor</th>
                    <th class="bg-dark text-white">Building</th>
                    <th class="bg-dark text-white">View</th>
                    <th class="bg-dark text-white">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$queryResult) {
                    echo "<tr><td colspan='7'>Error: " . mysqli_error($conn) . "</td></tr>";
                } elseif (mysqli_num_rows($queryResult) == 0) {
                    echo "<tr><td colspan='7'>No locations found.</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        ?>
                        <tr>
                            <td><?php echo $row['locationID'] ?></td>
                            <td><?php echo $row['roomCode'] ?></td>
                            <td><?php echo $row['roomType'] ?></td>
                            <td><?php echo $row['floor'] ?></td>
                            <td><?php echo $row['buildingName'] ?></td>
                            <td><a href="../viewlocation.php?locationid=<?php echo $row['locationID']; ?>">View</a></td>
                            <td><a href="deletelocation.php?locationid=<?php echo $row['locationID']; ?>" onclick="return confirmDelete()">Delete</a></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function confirmDelete() {
    return confirm("Are you sure you want to delete this location?");
}
</script>

<!-- JavaScript to display the session message as an alert when the page loads -->
<script>
    // Check if the message variable is set
    <?php if (isset($_SESSION['message'])) : ?>
        // Display the message as an alert
        alert('<?php echo $_SESSION['message']; ?>');
    <?php
        // Unset the session message after displaying it
        unset($_SESSION['message']);
    endif;
    ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
