<?php
session_start();
include 'db.php';  // Ensure you include your database connection file

// Optional access control
if (!isset($_SESSION['employment_no'])) {
    die("Access denied.");
}

$employment_no = $_SESSION['employment_no'];

// Check if user is Support Staff
$stmt = $conn->prepare("SELECT role FROM staff WHERE employment_no = ?");
$stmt->bind_param("s", $employment_no);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']) && $role === 'Support Staff') {
    $consultation_id = $_POST['consultation_id'];

    // Delete consultation from database
    $stmt = $conn->prepare("DELETE FROM consultations WHERE id = ?");
    $stmt->bind_param("i", $consultation_id);
    $stmt->execute();
    $stmt->close();
}

// SQL query to retrieve all consultations
$sql = "SELECT c.id, c.patient_number, c.consultation_date, c.notes, c.diagnosis, c.follow_up_date, s.full_name AS physician_name
        FROM consultations c
        JOIN staff s ON c.physician_id = s.employment_no"; // Assuming 'physician_id' references 'employment_no' in staff
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Consultations</title>
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

        input[type="submit"] {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <h2>All Consultations</h2>
    <table>
        <thead>
            <tr>
                <th>Consultation ID</th>
                <th>Patient Number</th>
                <th>Consultation Date</th>
                <th>Notes</th>
                <th>Diagnosis</th>
                <th>Follow-up Date</th>
                <th>Physician Name</th>
                <?php if ($role === 'Support Staff'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["patient_number"]) ?></td>
                        <td><?= htmlspecialchars($row["consultation_date"]) ?></td>
                        <td><?= htmlspecialchars($row["notes"]) ?></td>
                        <td><?= htmlspecialchars($row["diagnosis"]) ?></td>
                        <td><?= htmlspecialchars($row["follow_up_date"]) ?></td>
                        <td><?= htmlspecialchars($row["physician_name"]) ?></td>
                        <?php if ($role === 'Support Staff'): ?>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="consultation_id" value="<?= htmlspecialchars($row["id"]) ?>">
                                    <input type="submit" name="delete" value="Delete">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No consultations found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
