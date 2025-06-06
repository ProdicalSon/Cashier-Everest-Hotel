<?php
$host = "localhost";
$user = "root";
$password = "";  
$database = "cashier_everest_hotel";

$conn = new mysqli("localhost", "root", "", "cashier_everest_hotel", 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
