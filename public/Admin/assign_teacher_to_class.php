<?php
    session_start();
    require_once "../../includes/db.php";

    if($_SESSION['role'] !== 'admin'){
        header('Location: admin_login.php');
        exit();
    }

    $message = "";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $teacher_id = $_POST['teacher_id'];
        $class_id = $_POST['class_id'];

        if(!empty($teacher_id) && !empty($class_id)){
            $check = $conn->prepare("select * from teacher_classes where teacher_id = ? and class_id = ?");
            $check->bind_param("ii", $teacher_id, $class_id);
            $check->execute();
            $result = $check->get_result();

            if($result->num_rows > 0){
                $message = "This teacher is already assigned to that class";
            } else {
                $stmt = $conn->prepare("insert into teacher_classes (teacher_id, class_id) values (?, ?)");
                $stmt->bind_param("ii", $teacher_id, $class_id);

                if($stmt->execute()){
                    $message = "Teacher_assigned successfully!";
                } else {
                    $message = "Error : ".$stmt->error;
                }
                $stmt->close();
                
            }
            $check->close();
        } else {
            $message = "please select both teacher and class.";
        }
    }
    $teachers = $conn->query("select id, first_name, last_name from teachers order by first_name");
    $classes = $conn->query("select id, name from classes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/assign_teacher_to_class.css">
</head>
<body>
    <section class="assign-teacher-to-class">
        <h2>Assign teacher to class</h2>
        <?php
            if(!empty($message)) {
        ?>
        <p><?= $message ?></p>
        <?php } ?>

        <form action="assign_teacher_to_class.php" method = "POST">
                <label for="teacher_id">Select teacher:</label>
                <select name="teacher_id" id="teacher_id" required>
                    <option value="">-- Select --</option>
                    <?php while($teacher = $teachers->fetch_assoc()) { ?>
                        <option value="<?= $teacher['id'] ?>">
                            <?= htmlspecialchars($teacher['first_name'].' '.$teacher['last_name']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="class_id">Select class:</label>
                <select name="class_id" id="class_id" required>
                    <option value="">-- Select --</option>
                    <?php while($class = $classes->fetch_assoc()) { ?>
                        <option value="<?= $class['id'] ?>">
                            <?= htmlspecialchars($class['name']); ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="submit"> Assign </button>
        </form>
    </section>
</body>
</html>