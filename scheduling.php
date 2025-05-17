<?php
session_start();
include 'db.php';

// Optional access control
if (!isset($_SESSION['employment_no'])) {
    die("Access denied.");
}

// Handle consultation form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['schedule_surgery'])) {
    $patientNumber = $_POST['patient_number'];
    $consultationDate = $_POST['consultation_date'];
    $notes = $_POST['notes'];
    $diagnosis = $_POST['diagnosis'];
    $followUpDate = !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null;
    $physicianId = $_POST['physician_id'];

    $sql = "INSERT INTO consultations (patient_number, consultation_date, notes, diagnosis, follow_up_date, physician_id)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $patientNumber, $consultationDate, $notes, $diagnosis, $followUpDate, $physicianId);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Consultation scheduled successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Handle surgery form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_surgery'])) {
    $surgeryCode = $_POST['surgery_code'];
    $name = $_POST['surgery_name'];
    $category = $_POST['category'];
    $anatomicalLocation = $_POST['anatomical_location'];
    $specialNeeds = $_POST['special_needs'];
    $patientNumber = $_POST['surgery_patient_number'];
    $surgeonId = $_POST['surgeon_id'];
    $nurseId = $_POST['nurse_id'];

    $sql = "INSERT INTO surgeries (surgery_code, name, category, anatomical_location, special_needs, patient_number, surgeon_id, nurse_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $surgeryCode, $name, $category, $anatomicalLocation, $specialNeeds, $patientNumber, $surgeonId, $nurseId);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Surgery scheduled successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule Consultation & Surgery</title>
</head>
<body>
    <h2>Schedule a New Consultation</h2>
    <form method="POST" action="">
        <label for="patient_number">Patient Number:</label>
        <input type="text" name="patient_number" required><br><br>

        <label for="physician_id">Physician ID:</label>
        <input type="text" name="physician_id" required><br><br>

        <label for="consultation_date">Consultation Date & Time:</label>
        <input type="datetime-local" name="consultation_date" required><br><br>

        <label for="notes">Notes:</label><br>
        <textarea name="notes" rows="4" cols="50"></textarea><br><br>

        <label for="diagnosis">Diagnosis:</label><br>
        <textarea name="diagnosis" rows="4" cols="50"></textarea><br><br>

        <label for="follow_up_date">Follow-Up Date:</label>
        <input type="date" name="follow_up_date"><br><br>

        <input type="submit" value="Schedule Consultation">
    </form>

    <h2>Schedule a New Surgery</h2>
    <form method="POST" action="">
        <input type="hidden" name="schedule_surgery" value="1">

        <label>Surgery Code:</label>
        <input type="text" name="surgery_code" required><br><br>

        <label>Surgery Name:</label>
        <input type="text" name="surgery_name" required><br><br>

        <label>Category:</label>
        <select name="category" required>
            <option value="H">Hospital</option>
            <option value="O">Outpatient</option>
        </select><br><br>

        <label>Anatomical Location:</label>
        <input type="text" name="anatomical_location"><br><br>

        <label>Special Needs:</label><br>
        <textarea name="special_needs" rows="4" cols="50"></textarea><br><br>

        <label>Patient Number:</label>
        <input type="text" name="surgery_patient_number" required><br><br>

        <label>Surgeon ID:</label>
        <input type="text" name="surgeon_id" required><br><br>

        <label>Nurse ID:</label>
        <input type="text" name="nurse_id" required><br><br>

        <input type="submit" value="Schedule Surgery">
    </form>
</body>
</html>
