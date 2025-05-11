<?php
session_start();
include 'db.php';

// Only patients can access this page
if (!isset($_SESSION['patient_number'])) {
    die("Access denied.");
}

// Fetch physicians only
$stmt = $conn->prepare("SELECT full_name, employment_no, address, specialty FROM staff WHERE role = 'Physician' ORDER BY full_name");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Physicians</title>
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
        }

        th {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>

<h2>Available Physicians</h2>

<table>
    <thead>
        <tr>
            <th>Employment No</th>
            <th>Name</th>
            <th>Address</th>
            <th>Specialty</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['employment_no']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= htmlspecialchars($row['specialty']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No physicians found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
