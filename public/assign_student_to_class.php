<?php

session_start();
require_once "../includes/db.php";


if(!($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'teacher')){
    header('location: teacher_login.php');
}

$students = $conn->query("select id, first_name, last_name from students");
$classes = $conn->query("select id, name  from classes");

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_id'])){
    $student_id = intval($_POST['assign_id']);
    $class_id = intval($_POST['class_id']);

    $stmt = $conn->prepare("update students set class_id = ? where id = ?");
    $stmt->bind_param("ii", $class_id, $student_id);
    $stmt->execute();
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Student to Class</title>
    <link rel="stylesheet" href="./CSS/assign_student_to_class.css">
</head>
<body>
    <section class="assign-student">
        <h2>Assign Student to Class</h2>

        <?php if (!empty($success)) : ?>
            <p class="success-message">Student was successfully assigned to class!</p>
        <?php endif; ?>

        <form method="POST">
            <label for="assign_id">Select Student:</label>
            <select name="assign_id" required>
                <option value="">-- Choose Student --</option>
                <?php while ($student = $students->fetch_assoc()) : ?>
                    <option value="<?= $student['id'] ?>">
                        <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="class_id">Select Class:</label>
            <select name="class_id" required>
                <option value="">-- Choose Class --</option>
                <?php while ($class = $classes->fetch_assoc()) : ?>
                    <option value="<?= $class['id'] ?>">
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Assign</button>
        </form>
    </section>
</body>
</html>
