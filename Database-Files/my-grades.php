<?php
session_start();

// Check if the user is logged in and determine user type
if(isset($_SESSION['usertype'])) {
    $usertype = $_SESSION['usertype'];
} else {
    // Redirect the user to the login page if not logged in
    header("location: login.php");
    exit(); // Terminate script execution after redirection
}

// Define visibility flags for menu items based on user type
$show_accounts = true;
$show_students = true;
$show_subjects = true;
$show_grades = true;
$show_mygrades = false;


// Update visibility flags based on user type
if($usertype == "REGISTRAR") {
    $show_accounts = false;
    $show_mygrades = false;
} elseif($usertype == "STUDENT") {
    $show_accounts = false;
    $show_students = false;
    $show_subjects = false;
    $show_grades = false;
    $show_mygrades = true;
    
}
?>

<br>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades Management - Arellano University Subject Advising System - AUSAS</title>
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
         a.active
        {
            background-color: #F1D9A7
        }
        .student-container {
            background-color: #f9f9f9;
            
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 30px auto 0;
            max-width: 400px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }
      </style>
</head>
<body style="margin: 50px; background: #f2f2f2;">

    <header>
        <h1>
        <img src="logo.png" width="80" height="80" align="center"> Arellano University Subject Advising System </img>
        </h1>
        <ul class="navbar">
        <a href="index.php"> Home </a>
        <?php if($show_accounts): ?>
        <a href="account-management.php"> Accounts </a>
        <?php endif; ?>
        <?php if($show_students): ?>
        <a href="students-management.php"> Students </a>
        <?php endif; ?>
        <?php if($show_subjects): ?>
        <a href="subjects-management.php"> Subjects </a>
        <?php endif; ?>
        <?php if($show_grades): ?>
        <a href="grade-management.php"> Grades </a>
        <?php endif; ?>
        <?php if($show_mygrades): ?>
        <a class="active" href="#MyGrade"> MyGrades </a>
        <?php endif; ?>
        <?php if($_SESSION['usertype'] == 'STUDENT'): ?>
        <a class = 'btn btn-primary btn-sm' href = 'change-password.php'>Change Password</a>
        <?php endif; ?>
        <a class = 'btn btn-primary btn-sm' href = 'logout.php'>Logout</a> 
        </ul>
    </header><br><br><br><br><br>
    <hr>

<?php

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Include database configuration
require_once "config.php";

// Fetch student's information
$sql_student_info = "SELECT * FROM tblstudents WHERE studentnumber = ?";
if ($stmt_student_info = mysqli_prepare($link, $sql_student_info)) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt_student_info, "s", $_SESSION['username']);
    
    // Execute statement
    if (mysqli_stmt_execute($stmt_student_info)) {
        // Get result
        $result_student_info = mysqli_stmt_get_result($stmt_student_info);
        
        // Check if there are rows
        if (mysqli_num_rows($result_student_info) > 0) {
            // Fetch student information
            $row_student_info = mysqli_fetch_assoc($result_student_info);
            
            // Store student information in session
            $_SESSION['student_info'] = $row_student_info;
            
            // Display student information
            
            echo "<div class='student-container'>";
            echo "<h2>Student Information</h2>";
            echo "<p>Student number: " . $row_student_info['studentnumber'] . "</p>";
            echo "<p>Name: " . $row_student_info['firstname'] . " " .$row_student_info['middlename'] . " " . $row_student_info['lastname'] . "</p>";
            echo "<p>Course: " . $row_student_info['course'] . "</p>";
            echo "<p>Year Level: " . $row_student_info['yearlevel'] . "</p>";
            echo "</div>";
             echo "<br>";
        } else {
            echo "<center>";
            echo "No student information available.";
            echo "</center>";
        }
    } else {
        echo "Error executing SQL statement: " . mysqli_error($link);
    }
} else {
    echo "Error preparing SQL statement: " . mysqli_error($link);
}

// Fetch student's grades from database
$sql_grades = "SELECT g.code, s.description, s.unit, g.grade FROM tblgrades g JOIN tblsubjects s ON g.code = s.code WHERE g.studentnumber = ?";
if ($stmt_grades = mysqli_prepare($link, $sql_grades)) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt_grades, "s", $_SESSION['username']);
    
    // Execute statement
    if (mysqli_stmt_execute($stmt_grades)) {
        // Get result
        $result_grades = mysqli_stmt_get_result($stmt_grades);
        
        // Check if there are grades
        if (mysqli_num_rows($result_grades) > 0) {
            // Display grades
            echo "<h2>List of Grades</h2>";
            echo "<table class='table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Subject Code</th><th>Description</th><th>Unit</th><th>Grade</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<br>";
            while ($row = mysqli_fetch_assoc($result_grades)) {
                echo "<tr>";
                echo "<td>" . $row['code'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['unit'] . "</td>";
                echo "<td>" . $row['grade'] . "</td>";
                echo "<td>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<center>";
            echo "No grades available.";
            echo "</center>";
        }
    } else {
        echo "Error executing SQL statement: " . mysqli_error($link);
    }
} else {
    echo "Error preparing SQL statement: " . mysqli_error($link);
}

mysqli_close($link);
?>
</body>
</html>
<br>
<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>