
<?php
session_start();
require_once "config.php";  // Ensure you have included your database connection configuration

if (!isset($_SESSION['username']) || !isset($_SESSION['usertype'])) {
    header("location: login.php");
    exit();
}

$usertype = $_SESSION['usertype'];

// Set visibility flags based on user type
$show_accounts = $show_students = $show_subjects = $show_grades = $show_logs = $show_about = $show_mygrades = $show_advising = false;

if ($usertype == "ADMINISTRATOR") {
    $show_accounts = $show_students = $show_subjects = $show_grades = $show_logs = $show_about = $show_advising = true;
} elseif ($usertype == "REGISTRAR") {
    $show_students = $show_subjects = $show_grades = $show_about = $show_advising = true;
}
  elseif ($usertype == "STUDENT") {
  	$show_mygrades = true;
    $student_id = $_SESSION['username']; // Assuming the username is the student ID
    // Retrieve the course of the logged-in student
    $student_course = null;
    $sql_student = "SELECT course FROM tblstudents WHERE studentnumber = ?";
    if ($stmt_student = mysqli_prepare($link, $sql_student)) {
        mysqli_stmt_bind_param($stmt_student, "s", $student_id);
        if (mysqli_stmt_execute($stmt_student)) {
            $result_student = mysqli_stmt_get_result($stmt_student);
            if ($row_student = mysqli_fetch_assoc($result_student)) {
                $student_course = $row_student['course'];
            }
        }
        mysqli_stmt_close($stmt_student);
    }

$availableSubjects = [];
    $sql = "SELECT s.code, s.description, s.unit FROM tblsubjects s 
            WHERE s.course = ? AND NOT EXISTS (
                SELECT 1 FROM tblgrades g WHERE g.code = s.code AND g.studentnumber = ? AND g.grade IS NOT NULL
            ) AND NOT EXISTS (
                SELECT 1 FROM tblsubjects pr
                LEFT JOIN tblgrades g ON g.code = pr.code AND g.studentnumber = ?
                WHERE pr.code IN (s.prerequisite1, s.prerequisite2, s.prerequisite3) AND (g.grade IS NULL OR g.grade = '')
            )";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $student_course, $student_id, $student_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $availableSubjects[] = $row;
            }
        } else {
            echo "Error retrieving available subjects.";
        }
    } else {
        echo "Error preparing the SQL statement.";
    }
}
?>

<br>
<!DOCTYPE html>
	<title> Main Page - Arellano University Subject Advising System - AUSAS</title>
	<head>
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

      </style>
	</head>
<body style="margin: 50px; background: #f2f2f2;">
	<header>
		<h1>
   		<img src="logo.png" width="80" height="80" align="center"> Arellano University Subject Advising System </img>
        </h1>
        <ul class="navbar">
        <a class="active" href="#home"> Home </a>
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
		<a href="grade-management.php"> Grades </a>
		<?php endif; ?>
		<?php if($show_mygrades): ?>
		<a href="my-grades.php"> MyGrades </a>
		<?php endif; ?>
		<?php if($_SESSION['usertype'] == 'STUDENT'): ?>
		<a class = 'btn btn-primary btn-sm' href = 'change-password.php'>Change Password</a>
		<?php endif; ?>

		<a class = 'btn btn-primary btn-sm' href = 'logout.php'>Logout</a> 
		</ul>
	
    </header><br><br><br><br>
<?php

	if(isset($_SESSION['usertype']))
	{
		
		echo "<br>";
		echo "<h1>Welcome, " . $_SESSION['username'] . "</h1>";
		echo "<h4>Account type: " . $_SESSION['usertype'] . "</h4><br>";
		echo "<hr>";
		echo "<br>";
	}
	else
	{
		header("location: login.php");
		
	}

?>

<?php if ($usertype == "STUDENT"): ?>
            <h2>Subjects that can be taken</h2>
            <br>
            <?php if (!empty($availableSubjects)): ?>
                <table class="table">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Description</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                    <?php foreach ($availableSubjects as $subjects): ?>
                    	<tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($subjects['code']); ?></td>
                            <td><?php echo htmlspecialchars($subjects['description']); ?></td>
                            <td><?php echo htmlspecialchars($subjects['unit']); ?></td>
                        </tr>
                    </tbody>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No subjects available to take at this time. Please check back later.</p>
            <?php endif; ?>
        <?php endif; ?>

</body>
</html>
 <footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>