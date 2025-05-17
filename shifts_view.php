<?php
session_start();
include 'db.php';

if (!isset($_SESSION['employment_no'])) {
    die("Access denied.");
}

$emp_no = $_SESSION['employment_no'];

// Consultations where employee is physician
$consultations_sql = "
    SELECT id, patient_number, consultation_date, notes, diagnosis, follow_up_date 
    FROM consultations 
    WHERE physician_id = ?
";
$consultations_stmt = $conn->prepare($consultations_sql);
$consultations_stmt->bind_param("s", $emp_no);
$consultations_stmt->execute();
$consultations_result = $consultations_stmt->get_result();

// Surgeries where employee is surgeon or nurse
$surgeries_sql = "
    SELECT id, surgery_code, name, category, anatomical_location, special_needs, patient_number, surgeon_id, nurse_id 
    FROM surgeries 
    WHERE surgeon_id = ? OR nurse_id = ?
";
$surgeries_stmt = $conn->prepare($surgeries_sql);
$surgeries_stmt->bind_param("ss", $emp_no, $emp_no);
$surgeries_stmt->execute();
$surgeries_result = $surgeries_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Shifts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f8fa;
            margin: 20px;
        }

        h2 {
            background-color: #2980b9;
            color: white;
            padding: 10px;
            text-align: center;
            margin-top: 40px;
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

<h2>My Consultations</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient Number</th>
            <th>Date</th>
            <th>Notes</th>
            <th>Diagnosis</th>
            <th>Follow-Up Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($consultations_result->num_rows > 0): ?>
            <?php while($row = $consultations_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['patient_number']) ?></td>
                    <td><?= htmlspecialchars($row['consultation_date']) ?></td>
                    <td><?= htmlspecialchars($row['notes']) ?></td>
                    <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                    <td><?= htmlspecialchars($row['follow_up_date']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No consultations found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>My Surgeries</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Surgery Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Anatomical Location</th>
            <th>Special Needs</th>
            <th>Patient Number</th>
            <th>Surgeon ID</th>
            <th>Nurse ID</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($surgeries_result->num_rows > 0): ?>
            <?php while($row = $surgeries_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["id"]) ?></td>
                    <td><?= htmlspecialchars($row["surgery_code"]) ?></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= $row["category"] == 'H' ? 'Hospital' : 'Outpatient' ?></td>
                    <td><?= htmlspecialchars($row["anatomical_location"]) ?></td>
                    <td><?= htmlspecialchars($row["special_needs"]) ?></td>
                    <td><?= htmlspecialchars($row["patient_number"]) ?></td>
                    <td><?= htmlspecialchars($row["surgeon_id"]) ?></td>
                    <td><?= htmlspecialchars($row["nurse_id"]) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9">No surgeries found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
