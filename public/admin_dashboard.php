<?php 
    session_start();
    if(!isset($_SESSION['admin_id'])){
        header("Location: admin_login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./CSS/admin_dashboard.css">
    <title>Document</title>
</head>

<body>
    <header>
        Welcome , admin
    </header>

    <section class="dashboard-nav">
        <a href="view_teachers.php">View All teachers</a>
        <a href="add_teacher.php">Add new Teacher</a>
        <a href="assign_teacher_to_class.php">Assign Teacher to A Class</a>
        <a href="view_classes.php">View Classes</a>
        <a href="logout.php">Logout</a>
    </section>
</body>
</html>