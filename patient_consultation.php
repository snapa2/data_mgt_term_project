<?php
session_start();
include 'db.php';

// Ensure patient is logged in
if (!isset($_SESSION['patient_number'])) {
    die("Access denied. Please log in as a patient.");
}

$patient_number = $_SESSION['patient_number'];

// Fetch consultations for this patient
$sql = "SELECT c.id, c.consultation_date, c.notes, c.diagnosis, c.follow_up_date, s.full_name AS physician_name
        FROM consultations c
        JOIN staff s ON c.physician_id = s.employment_no
        WHERE c.patient_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_number);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Consultations</title>
    <style>
        h2 {
            background-color: #2980b9;
            color: white;
            padding: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Your Consultations</h2>
    <table>
        <thead>
            <tr>
                <th>Consultation ID</th>
                <th>Consultation Date</th>
                <th>Notes</th>
                <th>Diagnosis</th>
                <th>Follow-up Date</th>
                <th>Physician Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["consultation_date"]) ?></td>
                        <td><?= htmlspecialchars($row["notes"]) ?></td>
                        <td><?= htmlspecialchars($row["diagnosis"]) ?></td>
                        <td><?= htmlspecialchars($row["follow_up_date"]) ?></td>
                        <td><?= htmlspecialchars($row["physician_name"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No consultations found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
