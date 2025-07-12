<?php 
session_start();
require_once "../includes/db.php";


if(!($_SESSION['role'] === 'teacher' || isset($_SESSION['teacher_id']))){
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

//get teacher's classes
$class_stmt = $conn->prepare("
    select c.id, c.name
    from teacher_classes tc
    join classes c on tc.class_id = c.id
    where tc.teacher_id = ?
");

$class_stmt->bind_param("i", $teacher_id);
$class_stmt->execute();
$class_result = $class_stmt->get_result();
$classes = $class_result->fetch_all(MYSQLI_ASSOC);

//Get terms

$term_result = $conn->query("select id , name from terms order by id");
$terms = $term_result->fetch_all(MYSQLI_ASSOC);

//Get subjects
$subject_result = $conn->query("select id, name from subjects order by id");
$subjects = $subject_result->fetch_all(MYSQLI_ASSOC);

$selected_class = $_GET['class'] ?? '';
$selected_term = $_GET['term'] ?? '';

$students = [];

if($selected_class && $selected_term){
    $student_stmt = $conn->prepare("select id, first_name, last_name from students where class_id = ?");
    $student_stmt->bind_param("i", $selected_class);
    $student_stmt->execute();

    $students_result = $student_stmt->get_result();
    $students = $students_result->fetch_all(MYSQLI_ASSOC);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    foreach($_POST['marks'] as $student_id => $subject_marks){
        foreach($subject_marks as $subject_id => $mark){
            $mark = trim($mark);
            if($mark === '' || !is_numeric($mark)) continue;

            $check_stmt = $conn->prepare(
                "select id from marks where student_id = ? and subject_id = ? and term_id = ?"
            );

            $check_stmt->bind_param("iii", $student_id, $subject_id, $selected_term);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if($check_result->num_rows > 0){
                $update_stmt = $conn->prepare("
                    update marks set marks = ? where student_id = ? and subject_id = ? and term_id = ?
                ");
                $update_stmt->bind_param("diii", $mark, $student_id, $subject_id, $selected_term);
                $update_stmt->execute();
            } else {
                $insert_stmt = $conn->prepare("
                insert into marks (student_id , subject_id, term_id, marks)
                values (?,?,?,?)
                ");
                $insert_stmt->bind_param("iiid", $student_id, $subject_id, $selected_term, $mark);
                $insert_stmt->execute();
            }
        }
    }
    header("Location: teacher_enter_marks.php?class=$selected_class&term=$selected_term&success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Marks</title>
    <link rel="stylesheet" href="./CSS/teacher_enter_marks.css">
</head>
<body>
    <section class="marks-entry">
        <h2>Enter Student Marks</h2>

        <form method="GET" class="filter-form">
            <label for="class">Select Class:</label>
            <select name="class" id="class" required>
                <option value="">--Choose--</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= $selected_class == $class['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="term">Select Term:</label>
            <select name="term" id="term" required>
                <option value="">--Choose--</option>
                <?php foreach ($terms as $term): ?>
                    <option value="<?= $term['id'] ?>" <?= $selected_term == $term['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($term['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">View Students</button>
        </form>

        <?php if (!empty($students)): ?>
            <form method="POST" class="marks-form">
                <input type="hidden" name="class" value="<?= $selected_class ?>">
                <input type="hidden" name="term" value="<?= $selected_term ?>">

                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <?php foreach ($subjects as $subject): ?>
                                <th><?= htmlspecialchars($subject['name']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <?php foreach ($subjects as $subject): ?>
                                    <?php
                                    // Load existing mark
                                    $mark_value = '';
                                    $mark_check = $conn->prepare("
                                        SELECT marks FROM marks 
                                        WHERE student_id = ? AND subject_id = ? AND term_id = ?
                                    ");
                                    $mark_check->bind_param("iii", $student['id'], $subject['id'], $selected_term);
                                    $mark_check->execute();
                                    $result = $mark_check->get_result();
                                    if ($row = $result->fetch_assoc()) {
                                        $mark_value = $row['marks'];
                                    }
                                    ?>
                                    <td>
                                        <input type="number" name="marks[<?= $student['id'] ?>][<?= $subject['id'] ?>]" value="<?= $mark_value ?>" step="0.01" min="0" max="100">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button type="submit" class="submit-btn">Save Marks</button>
            </form>
        <?php elseif ($selected_class && $selected_term): ?>
            <p>No students found in this class.</p>
        <?php endif; ?>
    </section>
</body>
</html>