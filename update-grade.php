<?php
require_once "config.php";
include("session-checker.php");

// Fetch student information if available
$student_info = [];
if(isset($_SESSION['student_info'])) {
    $student_info = $_SESSION['student_info'];
}

$grade = [
    'code' => '',
    'description' => '',
    'unit' => '',
    'grade' => ''
];

if (isset($_POST['btnsubmit'])) {
    $new_grade = $_POST['grade'];

    // Code for updating the grade...
    $sql = "UPDATE tblgrades SET grade = ? WHERE studentnumber = ? AND code = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $new_grade, $student_info['studentnumber'], $_GET['code']);
        if (mysqli_stmt_execute($stmt)) {
            // Log the update action
            $action = "Update";
            $module = "Grades Management";
            $performed_by = $_SESSION['username'];
            $date_logged = date("m/d/Y");
            $time_logged = date("h:i:s");
            $sql_logs = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt_logs = mysqli_prepare($link, $sql_logs)) {
                mysqli_stmt_bind_param($stmt_logs, "ssssss", $date_logged, $time_logged, $action, $module, $student_info['studentnumber'], $performed_by);
                if (mysqli_stmt_execute($stmt_logs)) {
                    echo "<script>alert('Grade Updated');</script>";
                    echo "<script>window.location.href='grade-management.php';</script>";
                    exit();
                } else {
                    echo "<font color='red'>Error inserting logs.</font>";
                }
            } else {
                echo "<font color='red'>Error preparing logs query.</font>";
            }
        } else {
            echo "<font color='red'>Error updating grade record.</font>";
        }
    } else {
        echo "<font color='red'>Error preparing SQL statement to update grade record.</font>";
    }
} else {
    // Loading the current values of the grade
    if (isset($_GET['code']) && !empty(trim($_GET['code']))) {
        $sql = "SELECT g.code, g.grade, s.description, s.unit FROM tblgrades g JOIN tblsubjects s ON g.code = s.code WHERE g.code = ? AND g.studentnumber = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $_GET['code'], $student_info['studentnumber']);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if ($row) {
                    $grade = $row;
                }
            } else {
                echo "<font color='red'>Error loading the current grade values.</font>";
            }
        } else {
            echo "<font color='red'>Error preparing SQL statement to load current grade values.</font>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Grade - AU Subject Advising System - AUSMS</title>
    <link rel="stylesheet" href="bootstrap.css">
    <style>
      :root{
            --bg-color: #ffffff;
            --text-color: #121212;
            --main-font: 2.2rem;
            --p-font: 1.1rem;
        }
        header {
            width: 100%;
            top: 0;
            right: 0;
            position: fixed;
            background: #829bed;
            box-shadow: 0px 2px 18px 0 rgb(129 162 182 / 20%);
            text-align: center;
            justify-content: space-between;
            padding: 10px 8%;
            transition: .3s


        }
        .header{
            position: absolute;
            top: 174px;
            width: 80%;
            margin-left: 4%;
        }

        .navbar{
            display: flex
            text-align: center;
        }
        .navbar a{
            font-size: var(--p-font);
            color: var(--text-color);
            font-weight: 600;
            padding: 5px 10px;
            margin: 0 10px;
            transition: all .40s ease;
        }
        li a.active{
            background-color: #F1D9A7
        }
        .navbar a:hover{
            background: var(--text-color);
            color: #fff
        }


        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            border-radius: 10px 10px 0 0;
            text-align: center;
            padding: 5px;
            background: #829bed

        }
        footer p{
                color: var(--text-color);
                font-size: 15px;
                font-weight: 600;
                letter-spacing: 2px;
        }
        body{
            background: #dfe9f5;
        }
        .wrapper{
            width:600px;
            padding:2rem 1rem;
            margin:50px auto;
            background color: #fff;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
        }

        form input{
            width: 92%;
            outline: none;
            border: 1px solid #fff;
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 20px;
            background: #e4e4e4;
        }
        form select{
            width: 92%;
            outline: none;
            border: 1px solid #fff;
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 20px;
            background: #e4e4e4;

        }
        button{
            font-size: 1rem;
            margin-top: 1.8rem;
            padding: 10px 0;
            border-radius: 20px;
            outline: none;
            border: none;
            width: 90%;
            color: #fff;
            cursor: pointer;
            background: rgb(17,107,143);
        }
        button:hover{
            background: rgba(17, 107, 143, 0.877);
        }
        input:focus{
            border: 1px solid rgb(192, 192, 192);
        }
      </style>
</head>
<body>
    <header>
        <h1>
        <img src="logo.png" width="80" height="80" align="center"> Arellano University Subject Advising System </img>
      </h1>
    </header><br><br>

    <div class="wrapper"><br>
        <!-- Display student information -->
        <?php if(!empty($student_info)): ?>
            <h2>Student Information</h2>
            <p>Student number: <?php echo $student_info['studentnumber']; ?></p>
            <p>Name: <?php echo $student_info['firstname'] . " " . $student_info['middlename'] . " " . $student_info['lastname']; ?></p>
            <p>Course: <?php echo $student_info['course']; ?></p>
            <p>Year Level: <?php echo $student_info['yearlevel']; ?></p><br>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST">
            <h3>Change the grade on this form and submit to update the grade.</h3>
            <p>Subject Code: <?php echo $grade['code']; ?></p>
            <p>Description: <?php echo $grade['description']; ?></p>
            <p>Unit: <?php echo $grade['unit']; ?></p>
            <p>Current Grade: <?php echo $grade['grade']; ?></p>

            Grade:
            <select name="grade" id="grade" required>
                <option value="">--Select Grade--</option>
                <option value="1.00">1.00</option>
                <option value="1.25">1.25</option>
                <option value="1.50">1.50</option>
                <option value="1.75">1.75</option>
                <option value="2.00">2.00</option>
                <option value="2.25">2.25</option>
                <option value="2.50">2.50</option>
                <option value="2.75">2.75</option>
                <option value="3.00">3.00</option>
                <!-- Add more options for grades as needed -->
            </select><br>
            <button onclick="showAlert()" href = "grade-management.php" name = "btnsubmit" value = "Update"> Update </button><br>
            <a href="grade-management.php">Cancel</a>
        </form>
    </div>

    
</body>
</html>
<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>


