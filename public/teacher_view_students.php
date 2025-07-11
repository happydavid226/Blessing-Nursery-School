<?php

session_start();

require_once "../includes/db.php";

if(!(isset($_SESSION['teacher_id']) || isset($_SESSION['admin_id']))){
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

$class_stmt = $conn->prepare("
    select c.id, c.name
    from teacher_classes tc 
    join classes c on tc.class_id = c.id
    where tc.teacher_id = ?
");

$class_stmt->bind_param("i", $teacher_id);
$class_stmt->execute();

$classes_result = $class_stmt->get_result();

$classes = [];

while($row = $classes_result->fetch_assoc()){
    $classes[] = $row;
}

$selected_class = $_GET['class'] ?? 'all';

if($selected_class === 'all'){
    $stmt = $conn->prepare("
    select s.first_name, s.last_name, c.name as class_name
    from students s
    join classes c on s.class_id = c.id
    where s.class_id in (
        select class_id from teacher_classes where teacher_id = ?
    )
    order by c.name, s.first_name
    ");
    $stmt->bind_param("i", $teacher_id);
} else {
    $stmt = $conn->prepare("
        select s.first_name , s.last_name , c.name as class_name
        from students s
        join classes c on s.class_id = c.id
        where s.class_id = ? and s.class_id in (
            select class_id from teacher_classes where teacher_id = ?
        )
        order by s.first_name
    ");
    $stmt->bind_param("ii", $selected_class, $teacher_id);
}
    $stmt->execute();
    $students = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher View Students</title>
    <link rel="stylesheet" href="./CSS/teacher_view_students.css">
</head>
<body>
    <section class="teacher-view">
        <h2>Students in Your Class(es)</h2>

        <form method="GET">
            <label for="class">Select Class:</label>
            <select name="class" id="class" onchange="this.form.submit()">
                <option value="all" <?= $selected_class === 'all' ? 'selected' : '' ?>>All</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= $selected_class == $class['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['class_name']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
