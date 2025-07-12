<?php 

session_start();
require_once "../../includes/db.php";

if($_SESSION['role'] != 'admin'){
    header('location: admin_login.php');
    exit();
}


if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("delete from teacher_classes where id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
}

$sql = "select ct.id, t.first_name, t.last_name, c.name as class_name
from teacher_classes ct join teachers t on ct.teacher_id = t.id
join classes c on ct.class_id = c.id
order by t.first_name";

$result = $conn->query($sql);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Teacher from Class</title>
    <link rel="stylesheet" href="../CSS/remove_teacher_from_class.css">
</head>
<body>
    <section class="remove-teacher">
        <h2>Remove Teacher from Class</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Class</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['class_name']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to remove this assignment?');">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
