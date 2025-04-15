<?php
require_once "config.php";
include("session-checker.php");

$error_message = "";
$unmet_prerequisites = [];

// Fetch student information if available
$student_info = [];
if(isset($_SESSION['student_info'])) {
    $student_info = $_SESSION['student_info'];
}

// Fetch subject codes and descriptions from tblsubjects for dropdow
$subject_info = [];
$prerequisites = [];
$sql_subjects = "SELECT code, description, prerequisite1, prerequisite2, prerequisite3 FROM tblsubjects WHERE course = ?";
if ($stmt_subjects = mysqli_prepare($link, $sql_subjects)) {
    mysqli_stmt_bind_param($stmt_subjects, "s", $student_info['course']);
    if(mysqli_stmt_execute($stmt_subjects)) {
        $result_subjects = mysqli_stmt_get_result($stmt_subjects);
        while ($row = mysqli_fetch_assoc($result_subjects)) {
            $subject_info[$row['code']] = $row['description'];
            $prerequisites[$row['code']] = [$row['prerequisite1'], $row['prerequisite2'], $row['prerequisite3']];
        }
    } else {
        $error_message = "Error: Unable to fetch subject codes and descriptions.";
    }
    mysqli_stmt_close($stmt_subjects);
} else {
    $error_message = "Error: Unable to prepare SQL statement to fetch subjects.";
}

if(isset($_POST['submit'])) {
    $code = $_POST['code'];
    $grade = $_POST['cmbgrade'];
    $canProceed = true;

    // Check prerequisites
    if (!empty($prerequisites[$code])) {
        foreach ($prerequisites[$code] as $prerequisite) {
            if (!empty($prerequisite)) {
                $sql_prereq = "SELECT grade FROM tblgrades WHERE studentnumber = ? AND code = ? AND grade IS NOT NULL AND grade != ''";
                if ($stmt_prereq = mysqli_prepare($link, $sql_prereq)) {
                    mysqli_stmt_bind_param($stmt_prereq, "ss", $student_info['studentnumber'], $prerequisite);
                    mysqli_stmt_execute($stmt_prereq);
                    $result_prereq = mysqli_stmt_get_result($stmt_prereq);
                    if (mysqli_num_rows($result_prereq) == 0) {
                        $unmet_prerequisites[] = $prerequisite;
                        $canProceed = false;
                    }
                }
            }
        }
    }
    // Check if code exists in subject codes
    if (!$canProceed) {
        $error_message = "<font color = 'red'> Unmet prerequisites: " . implode(', ', $unmet_prerequisites) . ". You cannot add a grade for this subject until all prerequisite courses are graded. </font>";
    } else {
        // Check if a grade already exists
        $sql_check_grade = "SELECT grade FROM tblgrades WHERE studentnumber = ? AND code = ?";
        if ($stmt_check_grade = mysqli_prepare($link, $sql_check_grade)) {
            mysqli_stmt_bind_param($stmt_check_grade, "ss", $student_info['studentnumber'], $code);
            mysqli_stmt_execute($stmt_check_grade);
            $result_check_grade = mysqli_stmt_get_result($stmt_check_grade);
            if (mysqli_num_rows($result_check_grade) > 0) {
                $error_message = "<font color='red'> Error: Grade already exists for this subject. </font>";
            } else {
                // Proceed with inserting new grade
                $sql_insert_grade = "INSERT INTO tblgrades (studentnumber, code, grade, encodedby, dateencoded) VALUES (?, ?, ?, ?, ?)";
                if($stmt_insert_grade = mysqli_prepare($link, $sql_insert_grade)) {
                    $encoded_by = $_SESSION['username'];
                    $date_encoded = date("Y-m-d"); // Use the correct date format for your database
                    mysqli_stmt_bind_param($stmt_insert_grade, "sssss", $student_info['studentnumber'], $code, $grade, $encoded_by, $date_encoded);
                    if(mysqli_stmt_execute($stmt_insert_grade)) {
                        // Insert into logs
                        $sql_logs = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                        if ($stmt_logs = mysqli_prepare($link, $sql_logs)) {
                            $action = "Add";
                            $module = "Grades Management";
                            $date_log = date("m/d/Y");
                            $time_log = date("h:i:s");
                            mysqli_stmt_bind_param($stmt_logs, "ssssss", $date_log, $time_log, $action, $module, $student_info['studentnumber'], $encoded_by);
                            if (mysqli_stmt_execute($stmt_logs)) {
                                echo "<script>alert('Grade created successfully.');</script>";
                                echo "<script>window.location.href='grade-management.php';</script>";
                            } else {
                                $error_message = "Error logging the action: " . mysqli_error($link);
                            }
                        } else {
                            $error_message = "Error preparing logs query: " . mysqli_error($link);
                        }
                        exit();
                    } else {
                        $error_message = "Error: Unable to insert data into tblgrades.";
                    }
                } else {
                    $error_message = "Error: Unable to prepare SQL statement for insertion.";
                }
            }
        } else {
            $error_message = "<font-color='red'>Error: Unable to prepare SQL statement to check existing grades. </font>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Grade - Arellano University Subject Advising System - AUSAS</title>
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
        label{
            font-weight: bold;
        }
      </style>

</head>
<body>
    <header>
        <h1>
        <img src="logo.png" width="80" height="80" align="center"> Arellano University Subject Advising System </img>
      </h1>
    </header><br><br><br><br>
    </header>

    <div class="wrapper"><br>
        <!-- Display student information if available -->
        <?php if(!empty($student_info)): ?>
            <h2>Student Information</h2>
            <p>Student number: <?php echo $student_info['studentnumber']; ?></p>
            <p>Name: <?php echo $student_info['firstname'] . " " . $student_info['middlename'] . " " . $student_info['lastname']; ?></p>
            <p>Course: <?php echo $student_info['course']; ?></p>
            <p>Year Level: <?php echo $student_info['yearlevel']; ?></p>
        <?php endif; ?>

        <?php if(!empty($error_message)): ?>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
        <h3>Fill up this form and submit to create a grade.</h3><br>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="code">Subject code:</label><br>
            <select name="code" id="code" required onchange="updateDescription()">
                <option value="">Select Subject code</option> <!-- Default placeholder option -->
                <?php foreach ($subject_info as $code => $description): ?>
                    <option value="<?php echo $code; ?>"><?php echo $code; ?></option>
                <?php endforeach; ?>
            </select><br>
            <label for="description">Description:</label><br>
            <p id="description"></p><br>
            <label for="prerequisites">Prerequisites:</label><br>
            <p id="prerequisites"></p><br>
            <label for="cmbgrade">Grade:</label><br>
            <select name="cmbgrade" id="cmbgrade" required>
                <option value="">-- Select Grade --</option>
                <option value="1.00">1.00</option>
                <option value="1.25">1.25</option>
                <option value="1.50">1.50</option>
                <option value="1.75">1.75</option>
                <option value="2.00">2.00</option>
                <option value="2.25">2.25</option>
                <option value="2.50">2.50</option>
                <option value="2.75">2.75</option>
                <option value="3.00">3.00</option>
                
            </select><br>
            <input type="submit" name="submit" value="Submit">
            <a href="grade-management.php">Cancel</a>
        </form>
  </div>
  
   <footer>
        <p>Copyright © 2024 AUSAS. By Rafael Villena</p>
</footer>

    <script>
        var subjectInfo = <?php echo json_encode($subject_info); ?>;
        var prerequisites = <?php echo json_encode($prerequisites); ?>;

        function updateDescription() {
            var code = document.getElementById("code").value;
            var description = subjectInfo[code] || "";
            var prerequisiteText = prerequisites[code] ? prerequisites[code].filter(Boolean).join(", ") : "None";
            document.getElementById("description").textContent = description;
            document.getElementById("prerequisites").textContent = prerequisiteText;
        }
    </script>
</body>
</html>

<?php
if(!isset($_SESSION['username'])){
    header("location: login.php");
}
?>