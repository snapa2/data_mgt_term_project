<?php
include 'db.php';
session_start();

$patient_number = $_POST['patient_number'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT password FROM patients WHERE patient_number = ?");
$stmt->bind_param("s", $patient_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // Redirect to patient portal after successful login
        $_SESSION['patient_number'] = $patient_number;

        header("Location: patient_portal.html");
        exit();  // Don't forget to exit to prevent further code execution
    } else {
        echo "Invalid password.";
    }
} else {
    echo "Patient not found.";
}
?>
