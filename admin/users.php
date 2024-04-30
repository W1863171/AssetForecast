<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("location: ../login.php");
    die();
}

// Set up default sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'userID';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role_filter']) ? $_GET['role_filter'] : '';
$manager_filter = isset($_GET['manager_filter']) ? $_GET['manager_filter'] : '';

$sql = "SELECT u.userID, u.firstName, u.surname, u.email, u.role, 
               CONCAT(m.firstName, ' ', m.surname) AS managerName,
               u.createdAt, u.updatedAt
        FROM user u
        LEFT JOIN user m ON u.managerID = m.userID
        WHERE 1";


if (!empty($search)) {
    $sql .= " AND u.firstName LIKE '%$search%' OR u.surname LIKE '%$search%'";
}

if (!empty($role_filter)) {
    $sql .= " AND u.role = '$role_filter'";
}

if (!empty($manager_filter)) {
    // $manager_filter contains the concatenated first name and surname
    $sql .= " AND CONCAT(m.firstName, ' ', m.surname) = '$manager_filter'";
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
    <title>Users</title>
    <style>
        a {text-decoration:none}
    </style>
</head>
<body class="bg-dark text-white">
<?php
// Include the navigation bar
include 'adminnavbar.php';
?>

<div class="container">
    <hr>
    <h1>Users - Admin</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form action="users.php" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Name" name="search" value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="users.php" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <select name="role_filter" class="form-control">
                            <option value="">Filter By Role</option>
                            <?php
                            $role_query = "SELECT DISTINCT role FROM user ORDER BY role";
                            $role_result = mysqli_query($conn, $role_query);
                            while ($role_row = mysqli_fetch_assoc($role_result)) {
                                $selected = ($role_row['role'] == $role_filter) ? 'selected' : '';
                                echo "<option value='" . $role_row['role'] . "' $selected>" . $role_row['role'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select name="manager_filter" class="form-control">
                            <option value="">Filter By Manager</option>
                            <?php
                            $manager_query = "SELECT DISTINCT CONCAT(firstName, ' ', surname) AS managerName FROM user ORDER BY managerName";
                            $manager_result = mysqli_query($conn, $manager_query);
                            while ($manager_row = mysqli_fetch_assoc($manager_result)) {
                                $selected = ($manager_row['managerName'] == $manager_filter) ? 'selected' : '';
                                echo "<option value='" . $manager_row['managerName'] . "' $selected>" . $manager_row['managerName'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="users.php" class="btn btn-secondary">Clear Filters</a>
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
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=userID&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">User ID <?php echo ($sort_by === 'userID') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=firstName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">First Name <?php echo ($sort_by === 'firstName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=surname&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Surname <?php echo ($sort_by === 'surname') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=email&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Email <?php echo ($sort_by === 'email') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=role&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Role <?php echo ($sort_by === 'role') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=managerName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Manager <?php echo ($sort_by === 'managerName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=createdAt&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Created At <?php echo ($sort_by === 'createdAt') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark"><a class="bg-dark text-white" href="users.php?sort_by=updatedAt&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Updated At <?php echo ($sort_by === 'updatedAt') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                    <th class="bg-dark text-white">Edit</th>
                    <th class="bg-dark text-white">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Checking for errors
                if (!$queryResult) {
                    echo "<tr><td colspan='8'>Error: " . mysqli_error($conn) . "</td></tr>";
                } elseif (mysqli_num_rows($queryResult) == 0) {
                    echo "<tr><td colspan='8'>No users found.</td></tr>";
                } else {
                    // Displaying table
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        ?>
                        <tr>
                            <td><?php echo $row['userID'] ?></td>
                            <td><?php echo $row['firstName'] ?></td>
                            <td><?php echo $row['surname'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['role'] ?></td>
                            <td><?php echo $row['managerName'] ?></td>
                            <td><?php echo $row['createdAt'] ?></td>
                            <td><?php echo $row['updatedAt'] ?></td>
                            <td><a href="edituser.php?userid=<?php echo $row['userID']; ?>">Edit</a></td>

                            <td><a href="deleteuser.php?userid=<?php echo $row['userID'] ?>">Delete</a></td>
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
