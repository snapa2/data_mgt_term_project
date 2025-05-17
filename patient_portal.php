<?php
session_start();
include 'db.php';

// Ensure the user is logged in (we will check for the patient number in the session)
if (!isset($_SESSION['patient_number'])) {
    // If not logged in, redirect to the login page
    exit();
}

// Fetch patient data from the database
$patient_number = $_SESSION['patient_number'];
$stmt = $conn->prepare("SELECT patient_number, name, gender, dob, patient_type FROM patients WHERE patient_number = ?");
$stmt->bind_param("s", $patient_number);
$stmt->execute();
$stmt->store_result();

// If the patient exists, fetch their data
if ($stmt->num_rows > 0) {
    $stmt->bind_result($patient_number, $name, $gender, $dob, $patient_type);
    $stmt->fetch();
} else {
    echo "Patient not found.";
    exit();
}

// Update the patient data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the data from the form
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $patient_type = $_POST['patient_type'];

    // Update the database with the new values
    $update_stmt = $conn->prepare("UPDATE patients SET name = ?, gender = ?, dob = ?, patient_type = ? WHERE patient_number = ?");
    $update_stmt->bind_param("sssss", $name, $gender, $dob, $patient_type, $patient_number);

    if ($update_stmt->execute()) {
        echo "Your data has been updated successfully.";
    } else {
        echo "Error updating data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Portal</title>
    <style>
        h2 {
            background-color: #2980b9;
            color: white;
            padding: 10px;
        }
        form {
            width: 300px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Patient Portal</h2>

<form method="POST">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

    <label for="gender">Gender:</label>
    <select id="gender" name="gender" required>
        <option value="Male" <?= ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?= ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
    </select>

    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($dob) ?>" required>

    <label for="patient_type">Patient Type:</label>
    <select id="patient_type" name="patient_type" required>
        <option value="inpatient" <?= ($patient_type === 'inpatient') ? 'selected' : ''; ?>>Inpatient</option>
        <option value="outpatient" <?= ($patient_type === 'outpatient') ? 'selected' : ''; ?>>Outpatient</option>
    </select>

    <button type="submit">Update Information</button>
</form>

</body>
</html>

<?php
$conn->close();
?>
