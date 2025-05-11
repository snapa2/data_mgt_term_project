<?php 
session_start();
include 'db.php';

// Access control
if (!isset($_SESSION['employment_no'])) {
    die("Access denied.");
}

$is_support_staff = ($_SESSION['role'] === 'Support Staff');

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $is_support_staff) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM surgeries WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error deleting surgery: " . $conn->error);
    }
}

// Fetch surgeries
$sql = "SELECT id, surgery_code, name, category, anatomical_location, special_needs, patient_number, surgeon_id, nurse_id FROM surgeries";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Surgeries</title>
    <style>
        h2 {
            background-color: #2980b9;
            color: white;
            padding: 10px;
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

        .action-btn {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            color: white;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #27ae60;
        }

        .edit-btn:hover {
            background-color: #1e8449;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        form {
            display: inline;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">All Surgeries</h2>
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
                <?php if ($is_support_staff): ?>
                    <th>Actions</th>
                <?php endif; ?>
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
                        <td><?= htmlspecialchars($row["patient_number"]) ?></td>
                        <td><?= htmlspecialchars($row["surgeon_id"]) ?></td>
                        <td><?= htmlspecialchars($row["nurse_id"]) ?></td>
                        <?php if ($is_support_staff): ?>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this surgery?');">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <input type="submit" value="Delete" class="action-btn delete-btn">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="<?= $is_support_staff ? '10' : '9' ?>">No surgeries found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
