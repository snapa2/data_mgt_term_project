<?php
include 'db.php';

$employment_no = $_POST['employment_no'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, full_name, role FROM staff WHERE employment_no = ?");
$stmt->bind_param("s", $employment_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        if ($user['role'] === 'Nurse') {
            header("Location: nurse_portal.html");
            exit();
        }
        if ($user['role'] === 'Physician') {
            header("Location: physician_portal.html");
            exit();
        }
        if ($user['role'] === 'Surgeon') {
            header("Location: surgeon_portal.html");
            exit();
        }
        if ($user['role'] === 'Support Staff') {
            header("Location: supportstaff_portal.html");
            exit();
        } else {
            echo "Welcome, " . htmlspecialchars($user['full_name']) . "! Role: " . htmlspecialchars($user['role']);
        }
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "Employment number not found.";
}
?>
