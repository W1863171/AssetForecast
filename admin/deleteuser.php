<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php");
    die();
}

if(isset($_GET['userid'])) {
    $userid = $_GET['userid'];
    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");

    $sql = "DELETE FROM user WHERE userID = $userid";
    $result = mysqli_query($conn, $sql);
    header("location: users.php");
    die();
}
?>
