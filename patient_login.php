<?php
include 'db.php';

$patient_number = $_POST['patient_number'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT password FROM patients WHERE patient_number = ?");
$stmt->bind_param("s", $patient_number);
$stmt->execute();
$stmt->store_result();


if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    if (password_verify($password, $hashedPassword)) {
        echo "Login successful!";
    } else {
        echo "Invalid password.";
    }
} else {
    echo "Patient not found.";
}
?>