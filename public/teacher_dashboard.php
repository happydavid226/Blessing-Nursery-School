<?php

session_start();

if(!isset($_SESSION['teacher_id'])){
    header("Location: teacher_login.php");
    exit();
}

$teacher_name = $_SESSION['teacher_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="./CSS/teacher_dashboard.css">
</head>
<body>
    <section class="teacher-dashboard">
        <h1>Welcome, <?= htmlspecialchars($teacher_name) ?></h1>
        <nav>
            <ul>
                <li><a href="view_assigned_classes.php">view My Classes</a></li>
                <li><a href="view_students.php">View Students</a></li>
                <li><a href="teacher_logout.php">Logout</a></li>
            </ul>
        </nav>
    </section>
</body>
</html>