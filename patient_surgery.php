<?php 
session_start();
include 'db.php';

// Only patients can access this page
if (!isset($_SESSION['patient_number'])) {
    die("Access denied.");
}

$patient_number = $_SESSION['patient_number'];

// Fetch surgeries for the logged-in patient
$stmt = $conn->prepare("SELECT id, surgery_code, name, category, anatomical_location, special_needs, patient_number, surgeon_id, nurse_id FROM surgeries WHERE patient_number = ?");
$stmt->bind_param("s", $patient_number);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Surgeries</title>
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
                <th>Surgeon ID</th>
                <th>Nurse ID</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["surgery_code"]) ?></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= $row["category"] == 'H' ? 'Hospital' : 'Outpatient' ?></td>
                        <td><?= htmlspecialchars($row["anatomical_location"]) ?></td>
                        <td><?= htmlspecialchars($row["special_needs"]) ?></td>
                        <td><?= htmlspecialchars($row["surgeon_id"]) ?></td>
                        <td><?= htmlspecialchars($row["nurse_id"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No surgeries found for your account.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
