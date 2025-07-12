<?php
    session_start();
    if(!isset($_SESSION['admin_id'])){
        header("Location: admin_login.php");
        exit();
    }

    require_once "../../includes/db.php";

    $success = "";
    $error = "";

    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $first_name = htmlspecialchars(trim($_POST["first_name"]));
        $last_name = htmlspecialchars(trim($_POST["last_name"]));
        $email = htmlspecialchars(trim($_POST["email"]));
        $password = htmlspecialchars(trim($_POST["password"]));
        $role = 'teacher';

        if(empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($role)){
            echo "<script>alert('All fields are required');</script>";
        } else {
            $check = $conn->prepare("select * from teachers where email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if($check->num_rows > 0){
                echo "<script>alert('Email already exists');</script>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
                $stmt = $conn->prepare("insert into teachers 
                (first_name, last_name, email, password, role)
                 values (?, ?, ?, ?, ?)");

                $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $role);

                if($stmt->execute()){
                    $success = "Teacher added Successfully!";
                } else {
                    $error = "Error : " . $stmt->error;
                }
                $stmt->close();
            }
            $check->free_result();
            $check->close();
            
        }
    }
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add</title>
    <link rel="stylesheet" href="../CSS/add_teacher.css">
</head>

<body>
    <section class="add-teacher">
        <h2>Add a new teacher</h2>
        <?php if($success) { ?>
        <p><?=$success ?> </p>
        <?php } else if ($error) { ?>
        <p><?=$error ?></p>
        <?php } ?>

        <form action="add_teacher.php" method="POST">
            <input type="text" name="first_name" placeholder="first name" required>
            <input type="text" name="last_name" placeholder="last name" required>
            <input type="email" name="email" placeholder="email" required>
            <input type="password" name="password" placeholder="password" required>
            <button type="submit">Add a teacher</button>
        </form>
        <p><a href="./admin_dashboard.php">back to dashboard</a></p>

    </section>
</body>
</html>