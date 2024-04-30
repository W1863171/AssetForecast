<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['role'])) {
    header("location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "AssetForecast");

// Set up default sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'taskID';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$room_filter = isset($_GET['room_filter']) ? $_GET['room_filter'] : '';
$building_filter = isset($_GET['building_filter']) ? $_GET['building_filter'] : '';
$site_filter = isset($_GET['site_filter']) ? $_GET['site_filter'] : '';
$engineer_filter = isset($_GET['engineer_filter']) ? $_GET['engineer_filter'] : '';
$operator_filter = isset($_GET['operator_filter']) ? $_GET['operator_filter'] : '';
$scheduler_filter = isset($_GET['scheduler_filter']) ? $_GET['scheduler_filter'] : '';
$approved_filter = isset($_GET['approved_filter']) ? $_GET['approved_filter'] : '';
$scheduled_filter = isset($_GET['scheduled_filter']) ? $_GET['scheduled_filter'] : '';
$taskstatus_filter = isset($_GET['taskstatus_filter']) ? $_GET['taskstatus_filter'] : '';

// Pagination settings
$limit = 1000; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

$sql_total = "SELECT COUNT(*) AS total 
                FROM maintenance_task mt
                INNER JOIN asset a ON mt.barcode = a.barcode
                INNER JOIN location l ON a.locationID = l.locationID
                INNER JOIN building b ON l.buildingID = b.buildingID
                LEFT JOIN user e ON mt.attendedBy = e.userID
                LEFT JOIN user o ON mt.approvedBy = o.userID
                LEFT JOIN user s ON mt.scheduledBy = s.userID
                WHERE mt.cancelledBy IS NULL";

$sql = "SELECT mt.taskID, mt.barcode, mt.taskStatus, mt.createdDate, mt.taskType, mt.description, mt.startedAt, 
               mt.completedAt, CONCAT(COALESCE(e.firstName, ''), ' ', COALESCE(e.surname, '')) AS attendedByName,
               CONCAT(COALESCE(o.firstName, ''), ' ', COALESCE(o.surname, '')) AS approvedByName,
               CONCAT(COALESCE(s.firstName, ''), ' ', COALESCE(s.surname, '')) AS scheduledByName, mt.scheduledDate
        FROM maintenance_task mt
        INNER JOIN asset a ON mt.barcode = a.barcode
        INNER JOIN location l ON a.locationID = l.locationID
        INNER JOIN building b ON l.buildingID = b.buildingID
        LEFT JOIN user e ON mt.attendedBy = e.userID
        LEFT JOIN user o ON mt.approvedBy = o.userID
        LEFT JOIN user s ON mt.scheduledBy = s.userID
        WHERE mt.cancelledBy IS NULL";


if (!empty($search)) {
    $sql .= " AND mt.taskID LIKE '%$search%' OR mt.barcode LIKE '%$search%'";
    $sql_total .= " AND mt.taskID LIKE '%$search%' OR mt.barcode LIKE '%$search%'";
}

if (!empty($room_filter)) {
    $sql .= " AND l.roomCode = '$room_filter'";
    $sql_total .= " AND l.roomCode = '$room_filter'";
}

if (!empty($building_filter)) {
    $sql .= " AND b.buildingName = '$building_filter'";
    $sql_total .= " AND b.buildingName = '$building_filter'";
}

if (!empty($site_filter)) {
    $sql .= " AND b.site = '$site_filter'";
    $sql_total .= " AND b.site = '$site_filter'";
}

if (!empty($engineer_filter)) {
    $sql .= " AND CONCAT(e.firstName, ' ', e.surname) LIKE '%$engineer_filter%'";
    $sql_total .= " AND CONCAT(e.firstName, ' ', e.surname) LIKE '%$engineer_filter%'";
}

if (!empty($operator_filter)) {
    $sql .= " AND CONCAT(o.firstName, ' ', o.surname) LIKE '%$operator_filter%'";
    $sql_total .= " AND CONCAT(o.firstName, ' ', o.surname) LIKE '%$operator_filter%'";
}

if (!empty($scheduler_filter)) {
    $sql .= " AND CONCAT(s.firstName, ' ', s.surname) LIKE '%$scheduler_filter%'";
    $sql_total .= " AND CONCAT(s.firstName, ' ', s.surname) LIKE '%$scheduler_filter%'";
}

if (!empty($approved_filter)) {
    if ($approved_filter === 'approved') {
        $sql .= " AND mt.taskStatus != 'Not Approved'";
        $sql_total .= " AND mt.taskStatus != 'Not Approved'";
    } elseif ($approved_filter === 'not_approved') {
        $sql .= " AND mt.taskStatus = 'Not Approved'";
        $sql_total .= " AND mt.taskStatus = 'Not Approved'";
    }
}

if (!empty($scheduled_filter)) {
    if ($scheduled_filter === 'scheduled') {
        $sql .= " AND mt.scheduledBy IS NOT NULL";
        $sql_total .= " AND mt.scheduledBy IS NOT NULL";
    } elseif ($scheduled_filter === 'not_scheduled') {
        $sql .= " AND mt.scheduledBy IS NULL";
        $sql_total .= " AND mt.scheduledBy IS NULL";
    }
}

if (!empty($taskstatus_filter)) {
    $sql .= " AND mt.taskStatus = '$taskstatus_filter'";
    $sql_total .= " AND mt.taskStatus = '$taskstatus_filter'";
}

// Add sorting to the SQL query
$sql .= " ORDER BY $sort_by $sort_order";
$sql .= " LIMIT $limit OFFSET $offset";

// Fetching data
$queryResult = mysqli_query($conn, $sql);
$totalResult = mysqli_query($conn, $sql_total);
$totalTasks = mysqli_fetch_assoc($totalResult)['total'];

// Calculate total pages
$totalPages = ceil($totalTasks / $limit);
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
    <title>Maintenance Tasks</title>
</head>
<body class="bg-dark text-white">
<?php include 'navbar.php'; ?>
<div class="container">
    <hr>
    <h1>Maintenance Tasks</h1>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form action="maintenancetasks.php" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Task ID or Barcode" name="search" value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="maintenancetasks.php" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <!-- Dropdown for room filter -->
                        <select name="room_filter" class="form-control">
                            <option value="">Filter By Room</option>
                            <?php
                            $room_query = "SELECT DISTINCT roomCode FROM location ORDER BY roomCode";
                            $room_result = mysqli_query($conn, $room_query);
                            while ($room_row = mysqli_fetch_assoc($room_result)) {
                                $selected = ($room_row['roomCode'] == $room_filter) ? 'selected' : '';
                                echo "<option value='" . $room_row['roomCode'] . "' $selected>" . $room_row['roomCode'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <!-- Dropdown for building filter -->
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
                        <!-- Dropdown for site filter -->
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
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form action="maintenancetasks.php" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- Dropdown for engineer filter -->
                                    <select name="engineer_filter" class="form-control">
                                        <option value="">Filter By Engineer</option>
                                        <?php
                                        $engineer_query = "SELECT DISTINCT CONCAT(firstName, ' ', surname) AS engineer_name FROM engineer e INNER JOIN user u ON e.userID = u.userID ORDER BY engineer_name";
                                        $engineer_result = mysqli_query($conn, $engineer_query);
                                        while ($engineer_row = mysqli_fetch_assoc($engineer_result)) {
                                            $selected = ($engineer_row['engineer_name'] == $engineer_filter) ? 'selected' : '';
                                            echo "<option value='" . $engineer_row['engineer_name'] . "' $selected>" . $engineer_row['engineer_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <!-- Dropdown for approved by filter -->
                                    <select name="operator_filter" class="form-control">
                                        <option value="">Filter By Approved By</option>
                                        <?php
                                        $operator_query = "SELECT DISTINCT CONCAT(firstName, ' ', surname) AS operator_name FROM operator o INNER JOIN user u ON o.userID = u.userID ORDER BY operator_name";
                                        $operator_result = mysqli_query($conn, $operator_query);
                                        while ($operator_row = mysqli_fetch_assoc($operator_result)) {
                                            $selected = ($operator_row['operator_name'] == $operator_filter) ? 'selected' : '';
                                            echo "<option value='" . $operator_row['operator_name'] . "' $selected>" . $operator_row['operator_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <!-- Dropdown for scheduled by filter -->
                                    <select name="scheduler_filter" class="form-control">
                                        <option value="">Filter By Scheduled By</option>
                                        <?php
                                        $scheduler_query = "SELECT DISTINCT CONCAT(firstName, ' ', surname) AS scheduler_name FROM scheduler s INNER JOIN user u ON s.userID = u.userID ORDER BY scheduler_name";
                                        $scheduler_result = mysqli_query($conn, $scheduler_query);
                                        while ($scheduler_row = mysqli_fetch_assoc($scheduler_result)) {
                                            $selected = ($scheduler_row['scheduler_name'] == $scheduler_filter) ? 'selected' : '';
                                            echo "<option value='" . $scheduler_row['scheduler_name'] . "' $selected>" . $scheduler_row['scheduler_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- Dropdown for approved filter -->
                                    <select name="approved_filter" class="form-control">
                                        <option value="">Filter By Approval Status</option>
                                        <option value="approved" <?php echo ($approved_filter === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="not_approved" <?php echo ($approved_filter === 'not_approved') ? 'selected' : ''; ?>>Not Approved</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <!-- Dropdown for scheduled filter -->
                                    <select name="scheduled_filter" class="form-control">
                                        <option value="">Filter By Scheduling Status</option>
                                        <option value="scheduled" <?php echo ($scheduled_filter === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                        <option value="not_scheduled" <?php echo ($scheduled_filter === 'not_scheduled') ? 'selected' : ''; ?>>Not Scheduled</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <!-- Dropdown for Task Status filter -->
                                    <select name="taskstatus_filter" class="form-control">
                                        <option value="">Filter By Task Status</option>
                                        <?php
                                        $taskstatus_query = "SELECT DISTINCT taskStatus from maintenance_task ORDER BY taskStatus";
                                        $taskstatus_result = mysqli_query($conn, $taskstatus_query);
                                        while ($taskstatus_row = mysqli_fetch_assoc($taskstatus_result)) {
                                            $selected = ($taskstatus_row['taskStatus'] == $taskstatus_filter) ? 'selected' : '';
                                            echo "<option value='" . $taskstatus_row['taskStatus'] . "' $selected>" . $taskstatus_row['taskStatus'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-danger">Apply Filters</button>
                                    <a href="maintenancetasks.php" class="btn btn-secondary">Clear Filters</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div class='row'>
                    <div class="col-md-12"> 
                        <form id='timeframeForm'>
                            <div class="col-md-6">
                                <!-- Dropdown for selecting timeframe -->
                                <select id="timeframeDropdown" class="form-control">
                                    <option value="select">Select Time Frame</option>
                                    <option value="7_days">Within next 7 days</option>
                                    <option value="2_weeks">Within 2 weeks</option>
                                    <option value="1_month">Within 1 month</option>
                                    <option value="3_months">Within 3 months</option>
                                    <option value="6_months">Within 6 months</option>
                                    <option value="1_year">Within 1 year</option>
                                </select>
                            </div>
                            <hr>
                            <div class="col-md-6">
                                <!-- Button to trigger model execution -->
                                <button id="runModelButton" class="btn btn-info" id="submitButton">Generate New Tasks</button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <!-- Display tasks table -->
                    <table class="table table-hover">
                        <thead class="thead-dark">
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=taskID&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">ID <?php echo ($sort_by === 'taskID') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=barcode&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Asset Barcode <?php echo ($sort_by === 'barcode') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=taskStatus&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Status <?php echo ($sort_by === 'taskStatus') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=createdDate&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Created <?php echo ($sort_by === 'createdDate') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=taskType&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Type <?php echo ($sort_by === 'taskType') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=description&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Description <?php echo ($sort_by === 'description') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=startedAt&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Started <?php echo ($sort_by === 'startedAt') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=completedAt&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Completed <?php echo ($sort_by === 'completedAt') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=attendedByName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Engineer <?php echo ($sort_by === 'attendedByName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=approvedByName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Approved By <?php echo ($sort_by === 'approvedByName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=scheduledByName&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Scheduled By <?php echo ($sort_by === 'scheduledByName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark"><a class="bg-dark text-white" href="maintenancetasks.php?sort_by=scheduledDate&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Scheduled Date <?php echo ($sort_by === 'scheduledByName') ? ($sort_order === 'ASC' ? '&#9650;' : '&#9660;') : ''; ?></a></th>
                                <th class="bg-dark text-white">View</th>
                        </thead>
                        <tbody>
                            <?php
                            // Checking for errors
                            if (!$queryResult) {
                                echo "<tr><td colspan='14'>Error: " . mysqli_error($conn) . "</td></tr>";
                            } elseif (mysqli_num_rows($queryResult) == 0) {
                                echo "<tr><td colspan='14'>No tasks found.</td></tr>";
                            } else {
                                // Displaying table
                                while ($row = mysqli_fetch_assoc($queryResult)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['taskID'] ?></td>
                                        <td><?php echo $row['barcode'] ?></td>
                                        <td><?php echo $row['taskStatus'] ?></td>
                                        <td><?php echo $row['createdDate'] ?></td>
                                        <td><?php echo $row['taskType'] ?></td>
                                        <td><?php echo $row['description'] ?></td>
                                        <td><?php echo $row['startedAt'] ?></td>
                                        <td><?php echo $row['completedAt'] ?></td>
                                        <td><?php echo $row['attendedByName'] ?></td>
                                        <td><?php echo $row['approvedByName'] ?></td>
                                        <td><?php echo $row['scheduledByName'] ?></td>
                                        <td><?php echo $row['scheduledDate'] ?></td>
                                        <td><a href="viewmaintenancetask.php?taskID=<?php echo $row['taskID'] ?>">View</a></td>
                                        <!-- Add conditional columns/buttons based on user's role -->
                                        <?php if ($_SESSION['role'] === 'operator'):
                                            // Check if the task status is 'Not Approved'
                                            if ($row['taskStatus'] !== 'Not Approved') {
                                                echo "<td><button class='btn btn-danger btn-sm'>Already Approved</button></td>";
                                            } else {
                                                echo "<td><button class='btn btn-success btn-sm approve-btn' data-task-id='" . $row['taskID'] . "'>Approve</button></td>";
                                            }
                                        ?>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] === 'scheduler'): ?>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleModal" data-task-id="<?php echo $row['taskID']; ?>">
                                                    Assign
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                        
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        ?>
                        </tbody>
                    </table>
                    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="scheduleForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="scheduleModalLabel">Assign Task</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Dropdown for Engineer Name -->
                                        <select class="form-select" id="engineerName" required>
                                            <!-- Options will be dynamically populated from PHP -->
                                        </select>
                                        <hr>
                                        <!-- Date Field -->
                                        <input type="datetime-local" class="form-control" id="scheduledDate" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Assign Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pagination -->
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="maintenancetasks.php?page=<?php echo ($page - 1); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="maintenancetasks.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="maintenancetasks.php?page=<?php echo ($page + 1); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <!-- Include jQuery -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script>
                // Function to handle form submission
                $('#timeframeForm').submit(async function(event) {
                    event.preventDefault(); // Prevent default form submission

                    // Show spinner and disable button
                    $('#runModelButton').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...');

                    // Get the selected timeframe value
                    var selectedTimeframe = $('#timeframeDropdown').val();

                    // Make sure a valid timeframe is selected
                    if (selectedTimeframe === 'select') {
                        alert('Please select a timeframe.');
                        // Hide spinner and enable button
                        $('#runModelButton').prop('disabled', false).html('Generate New Tasks');
                        return;
                    }

                    try {
                        // Send an AJAX request to the PHP script
                        const response = await $.ajax({
                            url: 'predictmaintenance.php', // Path to the PHP script to handle the request
                            type: 'POST',
                            data: { timeframe: selectedTimeframe }, // Pass the selected timeframe value
                        });

                        // Handle the response from the server
                        if (response === 'Prediction completed.') {
                            alert('Prediction completed.');
                            // Redirect to maintenancetasks.php?approved_status=not_approved
                            window.location.href = 'maintenancetasks.php?approved_filter=not_approved';
                        } else if (response === 'Prediction failed.') {
                            alert('Prediction failed.');
                        }
                    } catch (error) {
                        console.error(error); // Log any errors to the console
                        alert('An error occurred during prediction.');
                    } finally {
                        // Hide spinner and enable button after request completes
                        $('#runModelButton').prop('disabled', false).html('Generate New Tasks');
                    }
                });
            </script>
            <script>
                $(document).ready(function() {
                    // Function to handle approve button click event
                    $('.approve-btn').click(function() {
                        var taskID = $(this).data('task-id');

                        // Send AJAX request to update task status
                        $.ajax({
                            url: '../AssetForecast/update_task_status.php',
                            type: 'POST',
                            data: {
                                taskID: taskID
                            },
                            success: function(response) {
                                // Display success message or handle response
                                alert(response);
                                // Reload the page to reflect the updated status
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                // Display error message or handle error
                                console.error(error);
                            }
                        });
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    // Function to populate engineer dropdown options
                    function populateEngineerDropdown() {
                        // Send AJAX request to fetch engineer names
                        $.ajax({
                            url: 'get_engineers.php',
                            type: 'GET',
                            success: function(response) {
                                $('#engineerName').html(response);
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                    }

                    // Handle form submission
                    $('#scheduleForm').submit(function(event) {
                        event.preventDefault(); // Prevent default form submission

                        // Collect form data
                        var taskID = $('#scheduleModal').data('task-id');
                        var engineerID = $('#engineerName').val();
                        var scheduledDate = $('#scheduledDate').val();

                        // Send AJAX request to schedule_task.php
                        $.ajax({
                            url: 'schedule_task.php',
                            type: 'POST',
                            data: {
                                taskID: taskID,
                                engineerID: engineerID,
                                scheduledDate: scheduledDate
                            },
                            success: function(response) {
                                alert(response); // Display success message or handle response
                                $('#scheduleModal').modal('hide'); // Hide the modal
                                // Reload the page to reflect the updated scheduling
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error(error); // Log any errors to the console
                            }
                        });
                    });

                        // Triggered when the modal is about to be shown
                        $('#scheduleModal').on('show.bs.modal', function(event) {
                            // Get the button that triggered the modal
                            var button = $(event.relatedTarget);
                            // Extract task ID from data attribute
                            var taskID = button.data('task-id');
                            // Set task ID in the modal
                            $('#scheduleModal').data('task-id', taskID);
                            // Populate engineer dropdown options
                            populateEngineerDropdown();
                        });
                });
            </script>




            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            </body>
            </html>
