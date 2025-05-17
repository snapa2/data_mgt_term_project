<?php
session_start();
include 'db.php';

// Check if surgeon is logged in
if (!isset($_SESSION['employment_no']) || $_SESSION['role'] !== 'Surgeon') {
    die("Access denied.");
}

$employment_no = $_SESSION['employment_no'];

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $telephone_no = $_POST['telephone_no'];
    $contract = $_POST['contract'];
    $specialty = $_POST['specialty'];

    // Update allowed fields in staff table
    $stmt = $conn->prepare("UPDATE staff SET full_name=?, address=?, contract=?, specialty=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $address, $contract, $specialty, $id);
    $stmt->execute();
    $stmt->close();

    // Update telephone number
    $stmt2 = $conn->prepare("UPDATE telephones SET phone=? WHERE staff_id=?");
    $stmt2->bind_param("si", $telephone_no, $id);
    $stmt2->execute();
    $stmt2->close();
}

// Fetch surgeon data
$sql = "SELECT s.id, s.employment_no, s.full_name, s.gender, s.address, s.contract, s.specialty,
               t.phone AS telephone_no
        FROM staff s
        LEFT JOIN telephones t ON s.id = t.staff_id
        WHERE s.employment_no = ? AND s.role = 'Surgeon'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employment_no);
$stmt->execute();
$result = $stmt->get_result();
$surgeon = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Surgeon Profile</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
    }
    th {
      background-color: #2980b9;
      color: white;
    }
    input[type="text"] {
      width: 100%;
      padding: 5px;
    }
    input[type="submit"] {
      padding: 5px 10px;
      background-color: #2ecc71;
      color: white;
      border: none;
      cursor: pointer;
    }
    input[type="submit"]:hover {
      background-color: #27ae60;
    }
  </style>
</head>
<body>

<h2>Your Profile</h2>

<form method="POST">
  <table>
    <tr>
      <th>Employment No</th>
      <td><?= htmlspecialchars($surgeon['employment_no']) ?></td>
    </tr>
    <tr>
      <th>Name</th>
      <td><input type="text" name="name" value="<?= htmlspecialchars($surgeon['full_name']) ?>"></td>
    </tr>
    <tr>
      <th>Gender</th>
      <td><?= htmlspecialchars($surgeon['gender']) ?></td>
    </tr>
    <tr>
      <th>Address</th>
      <td><input type="text" name="address" value="<?= htmlspecialchars($surgeon['address']) ?>"></td>
    </tr>
    <tr>
      <th>Telephone No</th>
      <td><input type="text" name="telephone_no" value="<?= htmlspecialchars($surgeon['telephone_no']) ?>"></td>
    </tr>
    <tr>
      <th>Contract</th>
      <td><input type="text" name="contract" value="<?= htmlspecialchars($surgeon['contract']) ?>"></td>
    </tr>
    <tr>
      <th>Specialty</th>
      <td><input type="text" name="specialty" value="<?= htmlspecialchars($surgeon['specialty']) ?>"></td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="hidden" name="id" value="<?= $surgeon['id'] ?>">
        <input type="submit" name="update" value="Update">
      </td>
    </tr>
  </table>
</form>

</body>
</html>

<?php $conn->close(); ?>
