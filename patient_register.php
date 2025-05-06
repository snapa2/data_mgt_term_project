<?php
include 'db.php';

function generatePatientNumber($length = 6) {
    return 'P' . str_pad(rand(0, 999999), $length, '0', STR_PAD_LEFT);
}

$patient_number = generatePatientNumber();
$name = $_POST['name'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$telephones = $_POST['telephone'];

$stmt = $conn->prepare("INSERT INTO patients (patient_number, name, gender, dob, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $patient_number, $name, $gender, $dob, $password);
$stmt->execute();

$phoneStmt = $conn->prepare("INSERT INTO patient_phones (patient_number, phone_number) VALUES (?, ?)");

foreach ($telephones as $phone) {
    if (!empty($phone)) {
        $phoneStmt->bind_param("ss", $patient_number, $phone);
        $phoneStmt->execute();
    }
}

echo "Registered successfully! Your Patient No is: <strong>$patient_number</strong><br><a href='patient.html'>Go to Login</a>";
?>
