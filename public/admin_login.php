<?php
    session_start();
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        require_once "../includes/db.php";

        $email = htmlspecialchars(trim($_POST['email']));
        echo $email;
        $password = htmlspecialchars(trim($_POST['password']));

        if(empty($email) || empty($password)){
            echo "<script>alert('Please fill in all fields'); </script>";
        } else {
            $stmt = $conn->prepare("select id, password from teachers where email = ? and role = 'admin'");
            $stmt->bind_param("s", $email);

            if($stmt->execute()){
                $result = $stmt->get_result();

                if($result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $hashed_password = $row['password'];

                    if(password_verify($password, $hashed_password)) {
                        $_SESSION['admin_id'] = $row['id'];
                        $_SESSION['role'] = 'admin';
                        header("Location: admin_dashboard.php");
                        exit();
                    } else {
                        echo "<script>alert('Incorrect password');</script>";
                    }
                } else {
                    echo "<script>alert('Email not found' + $email);</script>";
                }
            } else {
                echo " <script>alert('Error: " .$stmt->error . "' )</script>";
            }
            $stmt->close();
            $conn->close();
        }
    }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="./CSS/admin_login.css">
</head>

<body>
    <section class="login">
        <h2>Log in</h2>
        <form action="admin_login.php" method="post">
            <input type="email" placeholder="email" name="email" required>
            <input type="password" placeholder="password" name="password" required>
            <button type="submit">Log in</button>
        </form>
    </section>
</body>

</html>