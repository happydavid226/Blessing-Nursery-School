<?php
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])){
        require_once "../includes/db.php";

        //collect form data
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $dob = htmlspecialchars(trim($_POST['date']));
        $email = htmlspecialchars(trim($_POST['email']));
        $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST["gender"])) : "";

        $father_name = htmlspecialchars(trim($_POST['father_name']));
        $father_phone = htmlspecialchars(trim($_POST['father_phone']));
        $father_id = htmlspecialchars(trim($_POST['father_id']));

        $mother_name = htmlspecialchars(trim($_POST['mother_name']));
        $mother_phone = htmlspecialchars(trim($_POST['mother_phone']));
        $mother_id = htmlspecialchars(trim($_POST['mother_id']));

        $password1 = htmlspecialchars(trim($_POST['first_password']));
        $password2 = htmlspecialchars(trim($_POST['second_password']));

        $email_check = $conn->prepare("select id from students where email = ?");
        $email_check->bind_param("s", $email);
        $email_check->execute();
        $email_check->store_result();

        if($email_check->num_rows > 0){
            echo "<script> alert('Email already exists, please use another email')</script>";
        } 
        else if(
            empty($first_name) || empty($last_name)|| empty($dob)
            || empty($email) || empty($gender) || empty($father_name)
            || empty($father_phone) || empty($father_id) 
            ||  empty($mother_name) || empty($mother_phone) 
            || empty($mother_id) || empty($password1) 
            || empty($password2) 
        ) {
            echo "<script> alert('All fields are required.'); </script>";
        }
    

        else if($password1 !== $password2){
            echo "<script> alert('Passwords do not match'); </script>";
        } else {
            $hashed_password = password_hash($password1, PASSWORD_DEFAULT);
        
            $stmt = $conn->prepare("insert into students (first_name, last_name, date_of_birth, email, gender, father_name, father_phone, father_id, mother_name, mother_phone, mother_id, password)
            values (?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?);");
        
            $stmt->bind_param("ssssssssssss", $first_name, $last_name, $dob, $email, $gender, $father_name, $father_phone, $father_id, $mother_name, $mother_phone, $mother_id, $hashed_password);

            if($stmt->execute()){
                header("Location: login.php");
                exit();
            } else {
            echo "<script> alert('Error : " . $stmt->error ."'); </script>";
            }
            $email_check->close();
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
    <title>Sign up</title>
    <link rel="stylesheet" href="./CSS/signup.css">
</head>

<body>
    <section class="signup">
        <h2>Sign up</h2>
        <form action="Signup.php" method="POST">
            <h3>Student's Information</h3>
            <input type="text" placeholder="First Name" name="first_name">
            <input type="text" placeholder="Last Name" name="last_name">
            <input type="date" placeholder="date of birth" name="date">
            <input type="email" placeholder="Email" name="email">
            <div class="gender-group">
                <label for=""><input type="radio" value="Male" name="gender">Male</label>
                <label for=""><input type="radio" value="Female" name="gender">Female</label>
            </div>
            <h3>Father's information</h3>
            <input type="text" placeholder="Father's name" name="father_name">
            <input type="text" placeholder="Father's phone" name="father_phone">
            <input type="text" placeholder="Father's id" name="father_id">
            <h3>Mother's information</h3>
            <input type="text" placeholder="Mother's name" name="mother_name">
            <input type="text" placeholder="Mother's phone" name="mother_phone">
            <input type="text" placeholder="Mother's id" name="mother_id">
            <h3>Create a password</h3>
            <input type="password" placeholder="Password" name="first_password">
            <input type="password" placeholder="Confirm Password" name="second_password">
            <button type="submit" name="submit">Sign up</button>
        </form>
    </section>
</body>

</html>