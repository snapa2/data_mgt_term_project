<?php
include 'db.php';
session_start();

$employment_no = $_POST['employment_no'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, full_name, role FROM staff WHERE employment_no = ?");
$stmt->bind_param("s", $employment_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // ✅ Set session variables AFTER successful login
        $_SESSION['employment_no'] = $employment_no;
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        switch ($user['role']) {
            case 'Nurse':
                header("Location: nurse_portal.html");
                exit();
            case 'Physician':
                header("Location: physician_portal.html");
                exit();
            case 'Surgeon':
                header("Location: surgeon_portal.html");
                exit();
            case 'Support Staff':
                header("Location: supportstaff_portal.html");
                exit();
            default:
                echo "Welcome, " . htmlspecialchars($user['full_name']) . "! Role: " . htmlspecialchars($user['role']);
        }
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "Employment number not found.";
}
?>
