<?php

session_start();
require_once "../includes/db.php";
if(isset($_SESSION['teacher_id'])){
    header("Location: teacher_dashboard.php");
}

$message = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    if(!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("select id, first_name, last_name, password, role from teachers where email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows === 1){
            $teacher = $result->fetch_assoc();

            if(password_verify($password, $teacher['password'])){
                $_SESSION['teacher_id'] = $teacher['id'];
                $_SESSION['teacher_name'] = $teacher['first_name']. ' ' . $teacher['last_name'];
                $_SESSION['role'] = $teacher['role'];
                header("Location: teacher_dashboard.php");
                exit();
            } else {
                $message = "Incorrect password";
            }
        } else {
            $message = "No account with that email.";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <link rel="stylesheet" href="./CSS/teacher_login.css">
</head>
<body>
    <section class="teacher-login">
        <h2>Teacher Login</h2>
        <?php if (!empty($message)) { ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php } ?>

        <form action="teacher_login.php" method="POST">
            <input type="email" name="email" placeholder="email" required>
            <input type="password" name="password" placeholder="password" required>
            <button type="submit">Log in</button>
        </form>
    </section>
</body>
</html>