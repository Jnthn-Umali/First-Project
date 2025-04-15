<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['usertype'])) {
    header("location: login.php");
    exit;
}

$student_info = null;
$availableSubjects = [];
$studentNotFound = false; // Flag to indicate if a student was not found

// Check if a student number has been submitted
if (isset($_POST['studentnumber'])) {
    $student_number = $_POST['studentnumber'];

    // Fetch student information and their course
    $sql_student = "SELECT * FROM tblstudents WHERE studentnumber = ?";
    if ($stmt_student = mysqli_prepare($link, $sql_student)) {
        mysqli_stmt_bind_param($stmt_student, "s", $student_number);
        if (mysqli_stmt_execute($stmt_student)) {
            $result_student = mysqli_stmt_get_result($stmt_student);
            if ($row_student = mysqli_fetch_assoc($result_student)) {
                $student_info = $row_student;

                // Fetch subjects that match the student's course and are not graded
                $sql = "SELECT s.code, s.description, s.unit FROM tblsubjects s 
                        WHERE s.course = ? AND NOT EXISTS (
                            SELECT 1 FROM tblgrades g WHERE g.code = s.code AND g.studentnumber = ? AND g.grade IS NOT NULL
                        ) AND NOT EXISTS (
                            SELECT 1 FROM tblsubjects pr
                            LEFT JOIN tblgrades g ON g.code = pr.code AND g.studentnumber = ?
                            WHERE pr.code IN (s.prerequisite1, s.prerequisite2, s.prerequisite3) AND (g.grade IS NULL OR g.grade = '')
                        )";
                if ($stmt_subjects = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt_subjects, "sss", $student_info['course'], $student_number, $student_number);
                    if (mysqli_stmt_execute($stmt_subjects)) {
                        $result_subjects = mysqli_stmt_get_result($stmt_subjects);
                        while ($subject = mysqli_fetch_assoc($result_subjects)) {
                            $availableSubjects[] = $subject;
                        }
                    } else {
                        echo "Error executing subject availability check: " . mysqli_error($link);
                    }
                }
            } else {
                $studentNotFound = true; // Set flag if no student found
            }
        }
        mysqli_stmt_close($stmt_student);
    }
}
?>


<?php
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


// Update visibility flags based on user type
if($usertype == "REGISTRAR") {
    $show_accounts = false;
    $show_mygrades = false;
} elseif($usertype == "STUDENT") {
    $show_accounts = false;
    $show_students = false;
    $show_subjects = false;
    $show_grades = false;
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
        <a class="active" href="#Advising"> Advising Subject </a>
        <?php endif; ?>
        <?php if($show_grades): ?>
        <a href="grade-management.php"> Grades </a>
        <?php endif; ?>
        <?php if($_SESSION['usertype'] == 'STUDENT'): ?>
        <a class = 'btn btn-primary btn-sm' href = 'change-password.php'>Change Password</a>
        <?php endif; ?>
        <a class = 'btn btn-primary btn-sm' href = 'logout.php'>Logout</a> 
        </ul>
    </header><br><br><br><br><br><br>

      <main>
        <center>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            Search Student: <input type="text" name="studentnumber" placeholder = "Student no." required>
            <input type="submit" value="Search">
        </form>
    </center>
    <hr>
    <br>

        <?php if ($student_info): ?>
            <div class="student-container">
            <h3>Student Information</h3>
            <p>Student Number: <?php echo $student_info['studentnumber']; ?></p>
            <p>Name: <?php echo $student_info['firstname'] . " " . $student_info['lastname']; ?></p>
            <p>Course: <?php echo $student_info['course']; ?></p>
            <p>Year Level: <?php echo $student_info['yearlevel']; ?></p>
        </div>

            <h3>Subjects Available to Take</h3>
            <br>
            <table class="table">
                <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Description</th>
                    <th>Unit</th>
                </tr>
            </thead>
                <?php if (!empty($availableSubjects)): ?>
                    <?php foreach ($availableSubjects as $subject): ?>
                        <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($subject['code']); ?></td>
                            <td><?php echo htmlspecialchars($subject['description']); ?></td>
                            <td><?php echo htmlspecialchars($subject['unit']); ?></td>
                        </tr>
                    </tbody>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No available subjects to take at this moment.</td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <?php if ($studentNotFound): ?>
                <p>No student found with the number: <?php echo htmlspecialchars($student_number); ?>. Please try again.</p>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <br>
</body>
</html>
 <footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>