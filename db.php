<?php
$host = "localhost";
$user = "root";
$pass = ""; // default for XAMPP
$db = "newark_medical_clinic";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
