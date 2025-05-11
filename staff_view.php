<?php
session_start();
include 'db.php';

// Check if user is logged in and is Support Staff
if (!isset($_SESSION['employment_no']) || $_SESSION['role'] !== 'Support Staff') {
    die("Access denied.");
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    // Delete from telephones table
    $stmt_tel = $conn->prepare("DELETE FROM telephones WHERE staff_id = ?");
    if ($stmt_tel) {
        $stmt_tel->bind_param("i", $delete_id);
        $stmt_tel->execute();
        $stmt_tel->close();
    } else {
        die("Prepare failed (telephones): " . $conn->error);
    }

    // Delete from staff table
    $stmt_del = $conn->prepare("DELETE FROM staff WHERE id = ?");
    if ($stmt_del) {
        $stmt_del->bind_param("i", $delete_id);
        $stmt_del->execute();
        $stmt_del->close();
    } else {
        die("Prepare failed (staff): " . $conn->error);
    }
}

// Fetch staff list
$sql = "SELECT id, full_name, employment_no, role, address FROM staff ORDER BY role, full_name";
$result = $conn->query($sql);

$staff_by_role = [];
$roles = [];

while ($row = $result->fetch_assoc()) {
    $role = $row['role'];
    $roles[$role] = true;
    if (!isset($staff_by_role[$role])) {
        $staff_by_role[$role] = [];
    }
    $staff_by_role[$role][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Directory</title>
    <style>
        h2 {
            background-color: #2980b9;
            color: white;
            padding: 10px;
        }
        select {
            padding: 8px;
            margin: 10px 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 30px;
            display: none;
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
        form {
            display: inline;
        }
        .delete-btn {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<h2>Staff Directory</h2>

<label for="roleFilter">Filter by Role:</label>
<select id="roleFilter" onchange="filterRoles()">
    <option value="all">All</option>
    <?php foreach (array_keys($roles) as $role): ?>
        <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
    <?php endforeach; ?>
</select>

<?php foreach ($staff_by_role as $role => $members): ?>
    <div class="role-group" data-role="<?= htmlspecialchars($role) ?>">
        <h3><?= htmlspecialchars($role) ?>s</h3>
        <table>
            <tr>
                <th>Employment No</th>
                <th>Name</th>
                <th>Address</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <?php foreach ($members as $staff): ?>
                <tr>
                    <td><?= htmlspecialchars($staff['employment_no']) ?></td>
                    <td><?= htmlspecialchars($staff['full_name']) ?></td>
                    <td><?= htmlspecialchars($staff['address']) ?></td>
                    <td><?= htmlspecialchars($staff['role']) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                            <input type="hidden" name="delete_id" value="<?= $staff['id'] ?>">
                            <input type="submit" class="delete-btn" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endforeach; ?>

<script>
function filterRoles() {
    const selectedRole = document.getElementById('roleFilter').value;
    const roleGroups = document.querySelectorAll('.role-group');

    roleGroups.forEach(group => {
        const role = group.getAttribute('data-role');
        const table = group.querySelector('table');

        if (selectedRole === 'all' || selectedRole === role) {
            group.style.display = 'block';
            table.style.display = 'table';
        } else {
            group.style.display = 'none';
            table.style.display = 'none';
        }
    });
}

// Show all tables on load
window.onload = filterRoles;
</script>

</body>
</html>

<?php $conn->close(); ?>
