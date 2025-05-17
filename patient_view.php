<?php
session_start();
include 'db.php';

if (!isset($_SESSION['employment_no'])) {
    die("Access denied.");
}

$employment_no = $_SESSION['employment_no'];

// Check user role
$stmt = $conn->prepare("SELECT role FROM staff WHERE employment_no = ?");
$stmt->bind_param("s", $employment_no);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']) && $role === 'Support Staff') {
    $patient_number = $_POST['patient_number'];

    // Delete from patient_phones first (to satisfy foreign key constraint)
    $stmt1 = $conn->prepare("DELETE FROM patient_phones WHERE patient_number = ?");
    $stmt1->bind_param("s", $patient_number);
    $stmt1->execute();
    $stmt1->close();

    // Delete from patients table
    $stmt2 = $conn->prepare("DELETE FROM patients WHERE patient_number = ?");
    $stmt2->bind_param("s", $patient_number);
    $stmt2->execute();
    $stmt2->close();
}

// Fetch patient data
$sql = "
    SELECT 
        p.patient_number,
        p.name,
        p.gender,
        p.dob,
        p.patient_type,
        ph.phone_number
    FROM patients p
    LEFT JOIN patient_phones ph ON p.patient_number = ph.patient_number
    ORDER BY p.name;
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Directory</title>
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

    form {
      display: inline;
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

<h2>Patient Directory</h2>

<table>
  <thead>
    <tr>
      <th>Patient No</th>
      <th>Name</th>
      <th>Gender</th>
      <th>DOB</th>
      <th>Type</th>
      <th>Phone</th>
      <?php if ($role === 'Support Staff'): ?>
        <th>Actions</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['patient_number']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['gender']) ?></td>
          <td><?= htmlspecialchars($row['dob']) ?></td>
          <td><?= htmlspecialchars($row['patient_type']) ?></td>
          <td><?= htmlspecialchars($row['phone_number']) ?></td>
          <?php if ($role === 'Support Staff'): ?>
            <td>
              <form method="POST">
                <input type="hidden" name="patient_number" value="<?= htmlspecialchars($row['patient_number']) ?>">
                <input type="submit" name="delete" value="Delete">
              </form>
            </td>
          <?php endif; ?>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="<?= $role === 'Support Staff' ? '7' : '6' ?>">No patients found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

</body>
</html>

<?php $conn->close(); ?>
