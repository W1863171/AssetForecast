<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['role'])) {
  header("location: ../login.php");
  exit();
}

if ($_SESSION['role'] != 'admin' || $_SESSION['role'] != 'facilities manager'){
  header("location: home.php");
  exit();
}


if(isset($_GET['barcode']))
{
    $id = $_GET['barcode'];
    $conn = mysqli_connect("localhost", "root", "", "AssetForecast");


    $sql = "DELETE FROM asset WHERE barcode = $barcode";
    $result = mysqli_query($conn, $sql);
    header("location: assets.php"); die();

}


?>