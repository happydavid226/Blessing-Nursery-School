<?php
session_start();
require_once "../includes/db.php";


if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

//fetch student full name 

$student_stmt = $conn->prepare("select first_name, last_name from students where id = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student = $student_result->fetch_assoc();
$student_name = $student['first_name'] . ' ' . $student['last_name'];

// Fetch marks grouped by term and subject
$marks_stmt = $conn->prepare("
    SELECT t.name AS term_name, sub.name AS subject_name, m.marks
    FROM marks m
    JOIN terms t ON m.term_id = t.id
    JOIN subjects sub ON m.subject_id = sub.id
    WHERE m.student_id = ?
    ORDER BY t.id, sub.name
");

$marks_stmt->bind_param("i", $student_id);
$marks_stmt->execute();
$marks_result = $marks_stmt->get_result();

// Group data: $grouped_marks[term_name][subject_name] = mark
$grouped_marks = [];
while ($row = $marks_result->fetch_assoc()) {
    $term = $row['term_name'];
    $subject = $row['subject_name'];
    $mark = $row['marks'];
    $grouped_marks[$term][$subject] = $mark;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Marks</title>
    <link rel="stylesheet" href="./CSS/user_view_marks.css">
</head>
<body>
    <section class="user-marks-view">
        <h2><?= htmlspecialchars($student_name) ?> - My Marks</h2>

        <?php if (!empty($grouped_marks)): ?>
            <?php foreach ($grouped_marks as $term => $subjects): ?>
                <div class="term-section">
                    <h3>Term: <?= htmlspecialchars($term) ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects as $subject => $mark): ?>
                                <tr>
                                    <td><?= htmlspecialchars($subject) ?></td>
                                    <td><?= htmlspecialchars($mark) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No marks available at the moment.</p>
        <?php endif; ?>
    </section>
</body>
</html>