<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("location: login.php");
    exit();
}

// Check if the asset barcode is provided in the URL
if (!isset($_GET['barcode'])) {
    // Redirect back to the assets page if no barcode is provided
    header("location: assets.php");
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

// Query to fetch asset information along with location and building details
$sql = "SELECT 
            a.barcode, a.assettype, a.assetlifeexpectancy, a.installationdate, a.assetage,
            a.riskscore, a.riskrating, a.assetcondition, a.assetrepaircategory, a.environmentalrating,
            a.impactrating, l.roomcode, l.roomtype, l.floor, b.buildingcode, b.buildingname, b.site,
            b.address,b.postcode, fm.userid,
            CONCAT(u.firstname, ' ', u.surname) AS manager_name,
            u.email AS manager_email
        FROM 
            asset AS a
        JOIN 
            location AS l ON a.locationid = l.locationid
        JOIN 
            building AS b ON l.buildingid = b.buildingid
        LEFT JOIN 
            facilities_manager AS fm ON b.userid = fm.userid
        LEFT JOIN 
            user AS u ON fm.userid = u.userid
        WHERE
            a.barcode = '$barcode'";

$result = mysqli_query($conn, $sql);

// Check if the asset exists
if (mysqli_num_rows($result) == 0) {
    // Redirect back to the assets page if the asset does not exist
    header("location: assets.php");
    exit();
}

// Fetch the asset information
$asset = mysqli_fetch_assoc($result);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Asset Information</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
    <?php
    // Include the navigation bar
    include 'navbar.php';
    ?>
    <div class="container mt-5">
        <h1>View Asset Information - Barcode: <?php echo $asset['barcode']; ?></h1>
        <hr>
        <div class="card border-dark mb-3">
            <div class="card-body">
                <h3 class="card-title">Asset Information</h3>
                <hr>
                <p><strong>Barcode:</strong> <?php echo $asset['barcode']; ?></p>
                <p><strong>Asset Type:</strong> <?php echo $asset['assettype']; ?></p>
                <p><strong>Asset Life Expectancy:</strong> <?php echo $asset['assetlifeexpectancy']; ?></p>
                <p><strong>Installation Date:</strong> <?php echo $asset['installationdate']; ?></p>
                <p><strong>Asset Age:</strong> <?php echo $asset['assetage']; ?></p>
                <p><strong>Risk Score:</strong> <?php echo $asset['riskscore']; ?></p>
                <p><strong>Risk Rating:</strong> <?php echo $asset['riskrating']; ?></p>
                <p><strong>Asset Condition:</strong> <?php echo $asset['assetcondition']; ?></p>
                <p><strong>Asset Repair Category:</strong> <?php echo $asset['assetrepaircategory']; ?></p>
                <p><strong>Environmental Rating:</strong> <?php echo $asset['environmentalrating']; ?></p>
                <p><strong>Impacting Rating:</strong> <?php echo $asset['impactrating']; ?></p>
            </div>
        </div>
        <hr>
        <div class="card border-dark mb-3">
            <div class="card-body">
                <h3 class="card-title">Location Information</h3>
                <hr>
                <p><strong>Room Code:</strong> <?php echo $asset['roomcode']; ?></a></p>
                <p><strong>Room Type:</strong> <?php echo $asset['roomtype']; ?></p>
                <p><strong>Floor:</strong> <?php echo $asset['floor']; ?></p>
                <p><strong>Building Code:</strong> <a href="buildings.php?search=<?php echo $asset['buildingcode']; ?>"><?php echo $asset['buildingcode']; ?></a></p>
                <p><strong>Building Name:</strong> <a href="buildings.php?search=<?php echo $asset['buildingcode']; ?>"><?php echo $asset['buildingname']; ?></a></p>
                <p><strong>Site:</strong> <?php echo $asset['site']; ?></p>
                <p><strong>Address:</strong> <?php echo $asset['address']; ?></p>
                <p><strong>Postcode:</strong> <?php echo $asset['postcode']; ?></p>
                <p><strong>Manager Name:</strong> <?php echo $asset['manager_name']; ?></p>
                <p><strong>Manager Email:</strong> <?php echo $asset['manager_email']; ?></p>
            </div>
        </div>
        <hr>
        <div class="card border-dark mb-3">
            <div class="card-body">
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'facilities manager'): ?>
                    <a href="editasset.php?barcode=<?php echo $barcode; ?>" class="btn btn-primary">Edit Asset</a>
                    <a href="admin/deleteasset.php?barcode=<?php echo $barcode; ?>" class="btn btn-danger">Delete Asset</a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'engineer' || $_SESSION['role'] == 'admin' ): ?>
                    <a href="addreading.php?barcode=<?php echo $barcode; ?>" class="btn btn-success">Add Reading</a>
                <?php endif; ?>
                <a href="readings.php?search=<?php echo $barcode; ?>" class="btn btn-info">View Associated Readings</a>
                <a href="maintenancetasks.php?search=<?php echo $barcode; ?>" class="btn btn-warning">View Associated Maintenance Tasks</a>
            </div>
        </div>
        <hr>
        <div class = "card border-dark mb-3">
            <div class="card-body">             
                <h1 class="card-title">Asset Notes</h1>
                <hr>
                <!-- Add Note Button -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">Add Note</button>

                <!-- Existing Notes Section -->
                <div class="mt-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="bg-dark text-white">Note</th>
                                <th class="bg-dark text-white">Created By</th>
                                <th class="bg-dark text-white">Creation Date</th>
                                <th class="bg-dark text-white">Last Edited By</th>
                                <th class="bg-dark text-white">Last Edit Date</th>
                                <th class="bg-dark text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch notes associated with the asset barcode
                            $sql = "SELECT 
                                an.noteID,
                                an.note,
                                u.firstname AS createdBy_firstname,
                                u.surname AS createdBy_surname,
                                an.creationDate,
                                u2.firstname AS lastEditedBy_firstname,
                                u2.surname AS lastEditedBy_surname,
                                an.lastEditDate
                            FROM 
                                asset_note AS an
                            JOIN 
                                user AS u ON an.createdBy = u.userID
                            LEFT JOIN 
                                user AS u2 ON an.lastEditedBy = u2.userID
                            WHERE 
                                an.assetID = '$barcode'
                                AND
                                an.deletedDate IS NULL";

                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['note'] . "</td>";
                                    echo "<td>" . $row['createdBy_firstname'] . " " . $row['createdBy_surname'] . "</td>";
                                    echo "<td>" . $row['creationDate'] . "</td>";
                                    echo "<td>" . $row['lastEditedBy_firstname'] . " " . $row['lastEditedBy_surname'] . "</td>";
                                    echo "<td>" . $row['lastEditDate'] . "</td>";
                                    echo "<td>";
                                    // Edit Note Button
                                    echo "<button type='button' class='btn btn-warning editNoteButton' data-bs-toggle='modal' data-bs-target='#editNoteModal' data-note-id='" . $row['noteID'] . "'>Edit</button>";
                                    // Delete Note Button
                                    echo "<button type='button' class='btn btn-danger' onclick='deleteNote(" . $row['noteID'] . ")'>Delete</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No notes found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

               <!-- Edit Note Modal -->
                <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editNoteModalLabel">Edit Note</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            <form id="editNoteForm">
                                <!-- Hidden input field for the note ID -->
                                <input type="hidden" id="editNoteID" name="editNoteID">

                                <div class="mb-3">
                                    <label for="editNoteText" class="form-label">Note:</label>
                                    <textarea class="form-control" id="editNoteText" name="editNoteText" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>

                            </div>
                        </div>
                    </div>
                </div>


                <!-- Add Note Modal -->
                <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">

                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addNoteModalLabel">Add Note</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addNoteForm">
                                    <!-- Hidden input field for the barcode -->
                                    <input type="hidden" name="barcode" value="<?php echo $barcode; ?>">

                                    <div class="mb-3">
                                        <label for="noteText" class="form-label">Note:</label>
                                        <textarea class="form-control" id="noteText" name="noteText" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Note</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    // Function to handle form submission
                    $('#addNoteForm').submit(function (e) {
                        e.preventDefault(); // Prevent the default form submission

                        // Get the form data
                        var formData = $(this).serialize();

                        // Send the form data to the server using AJAX
                        $.ajax({
                            type: 'POST',
                            url: 'add_note.php',
                            data: formData,
                            success: function (response) {
                                // Handle the server response here
                                console.log(response); // For debugging
                                // Close the modal after successfully adding the note
                                $('#addNoteModal').modal('hide');
                                // Reload the current page to reflect the changes
                                location.reload(true); // Pass true to force reload from the server
                            },
                            error: function (xhr, status, error) {
                                // Handle errors here
                                console.error(xhr.responseText); // Log the error message
                                alert('An error occurred. Please try again.'); // Show an alert to the user
                            }
                        });
                    });

                    // Function to open the Add Note modal when the button is clicked
                    $('#addNoteButton').click(function () {
                        $('#addNoteModal').modal('show');
                    });

                    // Function to handle form submission for editing a note
                    $('#editNoteForm').submit(function (e) {
                        e.preventDefault(); // Prevent the default form submission

                        // Get the form data
                        var formData = $(this).serialize();

                        // Send the form data to the server using AJAX
                        $.ajax({
                            type: 'POST',
                            url: 'edit_note.php', // URL to edit note PHP script
                            data: formData,
                            success: function (response) {
                                // Handle the server response here
                                console.log(response); // For debugging

                                // Close the modal after successfully editing the note
                                $('#editNoteModal').modal('hide');

                                // Reload the current page to reflect the changes
                                location.reload(true); // Pass true to force reload from the server
                            },
                            error: function (xhr, status, error) {
                                // Handle errors here
                                console.error(xhr.responseText); // Log the error message
                                alert('An error occurred. Please try again.'); // Show an alert to the user
                            }
                        });
                    });

                    // Function to open the Edit Note modal when the edit button is clicked
                    $('.editNoteButton').click(function () {
                        // Get the note ID from the button's data attribute
                        var noteID = $(this).data('note-id');

                        // Populate the modal with the note ID
                        $('#editNoteID').val(noteID);

                        // Show the Edit Note modal
                        $('#editNoteModal').modal('show');
                    });


                    function deleteNote(noteID) {
                        // AJAX request to delete note
                        $.ajax({
                            url: 'delete_note.php',
                            type: 'POST',
                            data: {noteID: noteID},
                            success: function(response) {
                                // Refresh the page 
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    }
                </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>