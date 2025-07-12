<?php 

session_start();
require_once "../../includes/db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location : admin_login.php");
    exit();
}

$sql = "select t.id, t.first_name, t.last_name, t.email, t.role, c.name as class_name
        from teachers t
        left join teacher_classes ct on t.id = ct.teacher_id
        left join classes c on ct.class_id = c.id
        ";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/view_teachers.css">
    <title>All Teachers</title>
</head>
<body>
    <section class="view-teachers">
        <h1>All Regisered Teachers</h1>
        <div class="table-container">
            <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Assigned Class</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): ?>
                    <?php $count = 1; ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $count++  ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . " ". $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td><?= htmlspecialchars($row['class_name'] ?? "Not assigned") ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr><td colspan="5">No teachers added</td></tr>
                <?php endif; ?>
            </tbody>
            
        </table>
        </div>
        <div class="back">
            <a href="admin_dashboard.php"> Back to dashboard </a>
        </div>
    </section>
</body>
</html>