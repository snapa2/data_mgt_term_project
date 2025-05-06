<?php
include 'db.php';

$employment_no = $_POST['employment_no'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, full_name FROM staff WHERE employment_no = ?");
$stmt->bind_param("s", $employment_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        echo "Welcome, " . htmlspecialchars($user['full_name']) . "!";
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "Employment number not found.";
}
?>
