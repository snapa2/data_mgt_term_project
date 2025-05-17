<?php
session_start();
include 'db.php';

// Check if nurse is logged in
if (!isset($_SESSION['employment_no']) || $_SESSION['role'] !== 'Nurse') {
    die("Access denied.");
}

$employment_no = $_SESSION['employment_no'];

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $telephone_no = $_POST['telephone_no'];
    $grade = $_POST['grade'];
    $years = $_POST['years'];
    $annual_salary = $_POST['annual_salary'];

    // Update staff table
    $stmt = $conn->prepare("UPDATE staff SET full_name=?, address=?, annual_salary=?, grade=?, years=? WHERE id=?");
    $stmt->bind_param("ssddii", $name, $address, $annual_salary, $grade, $years, $id);
    $stmt->execute();
    $stmt->close();

    // Update telephones table
    $stmt2 = $conn->prepare("UPDATE telephones SET phone=? WHERE staff_id=?");
    $stmt2->bind_param("si", $telephone_no, $id);
    $stmt2->execute();
    $stmt2->close();
}

// Fetch logged-in nurse's data
$sql = "SELECT s.id, s.employment_no, s.full_name, s.gender, s.address, s.ssn, s.annual_salary, s.grade, s.years, t.phone AS telephone_no
        FROM staff s
        LEFT JOIN telephones t ON s.id = t.staff_id
        WHERE s.employment_no = ? AND s.role = 'Nurse'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employment_no);
$stmt->execute();
$result = $stmt->get_result();
$nurse = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Nurse Profile</title>
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
    input[type="text"], input[type="number"] {
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
      <td><?= htmlspecialchars($nurse['employment_no']) ?></td>
    </tr>
    <tr>
      <th>Name</th>
      <td><input type="text" name="name" value="<?= htmlspecialchars($nurse['full_name']) ?>"></td>
    </tr>
    <tr>
      <th>Gender</th>
      <td><?= htmlspecialchars($nurse['gender']) ?></td>
    </tr>
    <tr>
      <th>Address</th>
      <td><input type="text" name="address" value="<?= htmlspecialchars($nurse['address']) ?>"></td>
    </tr>
    <tr>
      <th>Telephone No</th>
      <td><input type="text" name="telephone_no" value="<?= htmlspecialchars($nurse['telephone_no']) ?>"></td>
    </tr>
    <tr>
      <th>SSN</th>
      <td><?= htmlspecialchars($nurse['ssn']) ?></td>
    </tr>
    <tr>
      <th>Annual Salary</th>
      <td><input type="number" step="0.01" name="annual_salary" value="<?= htmlspecialchars($nurse['annual_salary']) ?>"></td>
    </tr>
    <tr>
      <th>Grade</th>
      <td><input type="number" name="grade" value="<?= htmlspecialchars($nurse['grade']) ?>"></td>
    </tr>
    <tr>
      <th>Years</th>
      <td><input type="number" name="years" value="<?= htmlspecialchars($nurse['years']) ?>"></td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="hidden" name="id" value="<?= $nurse['id'] ?>">
        <input type="submit" name="update" value="Update">
      </td>
    </tr>
  </table>
</form>

</body>
</html>

<?php $conn->close(); ?>
