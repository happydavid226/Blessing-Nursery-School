<?php
session_start();
require_once "../../includes/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$classes_list = ['baby', 'middle', 'top'];


array_unshift($classes_list, 'all');

$selected_class = isset($_GET['class']) ? strtolower($_GET['class']) : 'all';

if (!in_array($selected_class, $classes_list)) {
    $selected_classs = 'all';
}

if ($selected_class == 'all') {
    $sql = "select s.id, s.first_name, s.last_name, c.name as class_name
    from students s
    left join classes c on s.class_id = c.id
    order by c.name";

    $stmt = $conn->prepare($sql);
} else {
    $sql = "select s.id, s.first_name, s.last_name, c.name as class_name
    from students s
    join classes c on s.class_id = c.id
    where c.name = ? order by s.first_name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selected_class);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>View Students</title>
    <link rel="stylesheet" href="../CSS/admin_view_students.css" />
</head>

<body>
    <section class="view-students">
        <h2>View Students</h2>

        <!-- Filter Form -->
        <form method="GET" action="admin_view_students.php" class="filter-form">
            <label for="class">Select Class:</label>
            <select name="class" id="class" onchange="this.form.submit()">
                <option value="all" <?= $selected_class === 'all' ? 'selected' : '' ?>>All</option>
                <option value="baby" <?= $selected_class === 'baby' ? 'selected' : '' ?>>Baby</option>
                <option value="middle" <?= $selected_class === 'middle' ? 'selected' : '' ?>>Middle</option>
                <option value="top" <?= $selected_class === 'top' ? 'selected' : '' ?>>Top</option>
            </select>
        </form>

        <!-- Students Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($student = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td><?= htmlspecialchars($student['class_name'] ?? 'Unassigned') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p><a href="./admin_dashboard.php">back to dashboard</a></p>
    </section>
    
</body>

</html>