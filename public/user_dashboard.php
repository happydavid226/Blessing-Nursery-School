<?php 
    session_start();
    var_dump($_SESSION);
    if(!isset($_SESSION['student_id'])){
        header('Location: login.php');
        exit();
    }
    require_once "../includes/db.php";

    $stmt = $conn->prepare("select first_name from students where id = ?");
    $stmt->bind_param("i", $_SESSION['student_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Welcome <?=  htmlspecialchars($student['first_name'])  ?> !</h1>
    <p>your marks will be displayed here soon</p>
    <li><a href="user_view_marks.php">View marks</a></li>
    <form action="logout.php" method="post">
        <button type="submit">Log out</button>
    </form>
</body>

</html>