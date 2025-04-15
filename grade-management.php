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
$show_advising = true;
$show_grades = true;
$show_logs = true;
$show_about = true;

// Update visibility flags based on user type
if($usertype == "REGISTRAR") {
    $show_accounts = false;
} elseif($usertype == "STUDENT") {
    $show_accounts = false;
    $show_students = false;
    $show_subjects = false;
    $show_grades = false;
    $show_logs = false;
    $show_about = false;
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
        <?php if($show_advising): ?>
        <a href="advising-subject.php"> Advising Subject </a>
        <?php endif; ?>
        <?php if($show_grades): ?>
        <a class="active" href="#Grade"> Grades </a>
        <?php endif; ?>
        <a class = 'btn btn-primary btn-sm' href = 'logout.php'>Logout</a> 
        </ul>
    </header><br><br><br><br>

            <?php
                
                require_once "config.php";

                if (!isset($_SESSION['usertype'])) {
                    header("location: login.php");
                    exit; // Added exit to stop further execution
                }
            ?>
        <br><br>
        <center>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                Search Student: <input type="text" name="studentnumber" placeholder = "Student no.">
                <input type="submit" value="Search">
            </form>
        </center>
        <hr>

            <?php

                if (isset($_POST['studentnumber'])) {
                    $student_number = $_POST['studentnumber'];
                    $sql = "SELECT * FROM tblstudents WHERE studentnumber = ?";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $student_number);
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);
                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $_SESSION['student_info'] = $row;

                                // Display student information
                                echo "<br>";
                                echo "<center>";
                                echo "<a class = 'btn btn-primary btn-sm' href = 'create-grade.php'>Add grade</a><br>";
                                echo "</center>";
                                echo "<div class='student-container'>";
                                echo "<h2>Student Information</h2>";
                                echo "<p>Student number: " . $row['studentnumber'] . "</p>";
                                echo "<p>Name: " . $row['firstname'] . " " . $row['middlename'] . " " . $row['lastname'] . "</p>";
                                echo "<p>Course: " . $row['course'] . "</p>";
                                echo "<p>Year Level: " . $row['yearlevel'] . "</p>";
                                echo "</div>";
                                echo "<br>";

                                // Fetch and display all grades for this student
                                $sql_grades = "SELECT g.code, s.description, s.unit, g.grade, g.encodedby, g.dateencoded FROM tblgrades g JOIN tblsubjects s ON g.code = s.code WHERE g.studentnumber = ?";
                                if ($stmt_grades = mysqli_prepare($link, $sql_grades)) {
                                    mysqli_stmt_bind_param($stmt_grades, "s", $student_number);
                                    if (mysqli_stmt_execute($stmt_grades)) {
                                        $result_grades = mysqli_stmt_get_result($stmt_grades);
                                        if (mysqli_num_rows($result_grades) > 0) {
                                            echo "<h2>List of Grades</h2>";
                                            echo "<table class='table'>";
                                            echo "<thead>";
                                            echo "<tr>";
                                            echo "<th>Subject Code</th><th>Description</th><th>Unit</th><th>Grade</th><th>Encoded By</th><th>Date Encoded</th><th>Action</th>";
                                            echo "</tr>";
                                            echo "</thead>";
                                            echo "<br>";
                                            while ($row_grade = mysqli_fetch_assoc($result_grades)) {
                                                echo "<tr>";
                                                echo "<td>" . $row_grade['code'] . "</td>";
                                                echo "<td>" . $row_grade['description'] . "</td>";
                                                echo "<td>" . $row_grade['unit'] . "</td>";
                                                echo "<td>" . $row_grade['grade'] . "</td>";
                                                echo "<td>" . $row_grade['encodedby'] . "</td>";
                                                echo "<td>" . $row_grade['dateencoded'] . "</td>";
                                                echo "<td>";
                                                echo "<a button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#exampleModalCenter' href = 'update-grade.php?code=" .$row_grade['code'] .  "'> Update </button>";
                                                echo "<a href='#' onclick='confirmAndDelete(\"" . $row_grade['code'] . "\")' class='btn btn-danger btn-sm'>Delete</a>";

                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                            echo "</table>";
                                        } else {
                                            echo "<center>";
                                            echo "No records found.";
                                            echo "</center>";
                                        }
                                    } else {
                                        echo "Error executing SQL statement: " . mysqli_error($link);
                                    }
                                    mysqli_stmt_close($stmt_grades);
                                } else {
                                    echo "Error preparing SQL statement: " . mysqli_error($link);
                                }
                            } else {
                                echo "<center>";
                                echo "No student found with the given student number.";
                                echo "</center>";
                            }
                        } else {
                            echo "Error executing SQL statement: " . mysqli_error($link);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        echo "Error preparing SQL statement: " . mysqli_error($link);
                    }
                    mysqli_close($link);
                }
            ?>
    <script>
        function confirmAndDelete(grade) {
            if (confirm("Are you sure you want to delete this grade record?")) {
                // Perform AJAX request to delete grade record
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "delete-grade.php?code=" + grade, true); // Changed "grade" to "code"
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert(xhr.responseText); // Show response from server (e.g., success message)
                        // You can also update the UI here if needed
                        location.reload();
                    }
                };
                xhr.send();
            }
        }


        function showMessage() {
            alert("Grade Created");
            window.location.href = "grade-management.php";
        }

        function showMessage() {
            alert("Grade Updated");
            window.location.href = "grade-management.php";
        }
    </script>
</body>
</html>
<br>
<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>