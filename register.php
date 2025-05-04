<?php
include 'db.php';

function generateEmploymentNo($length = 6) {
    return 'EMP' . str_pad(rand(0, 999999), $length, '0', STR_PAD_LEFT);
}

$employment_no = generateEmploymentNo();
$full_name = $_POST['full_name'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$ssn = $_POST['ssn'];
$role = $_POST['role'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$telephones = $_POST['telephone'];

$stmt = $conn->prepare("INSERT INTO staff (employment_no, full_name, gender, address, ssn, role, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $employment_no, $full_name, $gender, $address, $ssn, $role, $password);

if ($stmt->execute()) {
    $staff_id = $conn->insert_id;

    $phone_stmt = $conn->prepare("INSERT INTO telephones (staff_id, phone) VALUES (?, ?)");
    foreach ($telephones as $phone) {
        if (!empty(trim($phone))) {
            $phone_stmt->bind_param("is", $staff_id, $phone);
            $phone_stmt->execute();
        }
    }

    echo "Registered successfully! Your Employment No is: <strong>$employment_no</strong><br><a href='employee.html'>Go to Login</a>";
} else {
    echo "Error: " . $stmt->error;
}
?>
