<?php
session_start();
require_once "../includes/db.php";

if(!($_SESSION['role'] === 'teacher' || isset($_SESSION['teacher_id']))){
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

$class_stmt = $conn->prepare("
    select c.id , c.name from teacher_classes tc join classes c on tc.class_id = c.id where tc.teacher_id = ?
");


$class_stmt->bind_param("i", $teacher_id);
$class_stmt->execute();
$class_result = $class_stmt->get_result();
$classes = $class_result->fetch_all(MYSQLI_ASSOC);

//GET ALL TERMS

$term_result = $conn->query("select id, name from terms");
$terms  = $term_result->fetch_all(MYSQLI_ASSOC);

//Selected filters
$selected_class = $_GET['class'] ?? '';
$selected_term = $_GET['term'] ?? '';

$student_marks = [];

if($selected_class && $selected_term){
    $stmt = $conn->prepare("
    select s.id as student_id , s.first_name , s.last_name, sub.name as subject_name, m.marks
    from students s
    join marks m on s.id = m.student_id
    join subjects sub on m.subject_id = sub.id
    where s.class_id = ? and m.term_id = ?
    order by s.first_name, sub.name
    ");

    $stmt->bind_param("ii", $selected_class, $selected_term);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()){
        $student_id = $row['student_id'];
        $student_name = $row['first_name'] . ' ' . $row['last_name'];
        $subject = $row['subject_name'];
        $mark = $row['marks'];

        //group by student

        $student_marks[$student_id]['name'] = $student_name;
        $student_marks[$student_id]['marks'][$subject] = $mark;
    }

    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher View Marks</title>
    <link rel="stylesheet" href="./CSS/teacher_view_marks.css">
</head>
<body>
    <section class="teacher-marks-view">
        <h2>View Marks</h2>

        <form method="GET" class="filter-form">
            <label for="class">Class:</label>
            <select name="class" id="class" required>
                <option value="">--Select--</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= $selected_class == $class['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="term">Term:</label>
            <select name="term" id="term" required>
                <option value="">--Select--</option>
                <?php foreach ($terms as $term): ?>
                    <option value="<?= $term['id'] ?>" <?= $selected_term == $term['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($term['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">View</button>
        </form>

        <?php if ($selected_class && $selected_term): ?>
            <?php if (!empty($student_marks)): ?>
                <div class="marks-table-wrapper">
                    <?php foreach ($student_marks as $student): ?>
                        <div class="student-card">
                            <h3><?= htmlspecialchars($student['name']) ?></h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($student['marks'] as $subject => $mark): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($subject) ?></td>
                                            <td><?= htmlspecialchars($mark) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No marks found for selected class and term.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</body>
</html>
