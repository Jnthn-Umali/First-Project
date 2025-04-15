<?php
require_once "config.php";
include ("session-checker.php");

$subject_code = isset($_GET['code']) ? $_GET['code'] : (isset($_POST['code']) ? $_POST['code'] : null);

// Fetch all subjects organized by course
$subjects_by_course = [];
$sql_subjects_by_course = "SELECT code, course FROM tblsubjects WHERE code != ?";
if ($stmt = mysqli_prepare($link, $sql_subjects_by_course)) {
    mysqli_stmt_bind_param($stmt, "s", $subject_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects_by_course[$row['course']][] = $row['code'];
    }
    mysqli_stmt_close($stmt);
}

if (isset($_POST['btnsubmit'])) {
    $sql = "UPDATE tblsubjects SET description = ?, unit = ?, course = ?, prerequisite1 = ?, prerequisite2 = ?, prerequisite3 = ? WHERE code = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssss", $_POST['txtdescription'], $_POST['cmbunit'], $_POST['cmbcourse'], $_POST['cmbprerequisite1'], $_POST['cmbprerequisite2'], $_POST['cmbprerequisite3'], $_GET['code']);
        if (mysqli_stmt_execute($stmt)) {
            // Insert log entry
            $sql_logs = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            $date = date("m/d/Y"); // Adjust date format as needed
            $time = date("h:i:s");
            $action = 'Update';
            $module = 'Subject Management';
            $id = $_GET['code'];
            $performedby = $_SESSION['username'];

            if ($stmt_logs = mysqli_prepare($link, $sql_logs)) {
                mysqli_stmt_bind_param($stmt_logs, "ssssss", $date, $time, $action, $module, $id, $performedby);
                if (mysqli_stmt_execute($stmt_logs)) {
                    echo "<script>alert('Subject Updated Successfully and log recorded');</script>";
                    echo "<script>window.location.href='subjects-management.php';</script>";
                    exit();
                } else {
                    echo "<font color='red'>Error on inserting log entry.</font>";
                }
            } else {
                echo "<font color='red'>Error preparing log insertion query.</font>";
            }
        } else {
            echo "<font color='red'>Error updating subject record.</font>";
        }
    } else {
        echo "<font color='red'>Error preparing SQL statement to update subject record.</font>";
    }
}

if (isset($_GET['code']) && !empty(trim($_GET['code']))) {
    $sql = "SELECT * FROM tblsubjects WHERE code = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $_GET['code']);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $subject = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            echo "<font color='red'>Error loading the current subject values.</font>";
        }
    } else {
        echo "<font color='red'>Error preparing SQL statement to load current subject values.</font>";
    }
}
?>

<html>
<title> Update subject - AU Subject Advising System - AUSMS </title>
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
    </header><br><br><br><br>
	<div class="wrapper"><br>
	<center>
	<h2> Change the value on this form to update the subject. </h2><br>
	<form style="font-family:verdana;" action = "<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method = "POST">
	<input type = "text" placeholder = "Code" name = "txtcode" value = "<?php echo $subject['code']; ?>" readonly><br>
	 <input type = "text" placeholder = "Description" name = "txtdescription" value = "<?php echo $subject['description']; ?>" required><br>
	 Unit: <br><select name = "cmbunit" id = "cmbunit" required><br>
		<option value="">Current Unit : <?php echo $subject['unit']; ?></option><br>
			<option value="1"> 1  </option>
			<option value="2"> 2 </option>
			<option value="3"> 3 </option>
			<option value="4"> 4 </option>
			<option value="5"> 5 </option>
		</select> <br><br>
	 Course: <select name = "cmbcourse" id = "cmbcourse" required><br>
			<option value="Bachelor of Arts in English, Political Science, Psychology & History"> Bachelor of Arts in English, Political Science, Psychology & History </option>
			<option value="Bachelor of Performing Arts (Dance)"> Bachelor of Performing Arts (Dance) </option>
			<option value="Bachelor of Science in Criminology"> Bachelor of Science in Criminology </option>
			<option value="Bachelor of Science in Accountancy"> Bachelor of Science in Accountancy </option>
			<option value="Bachelor of Science in Computer Science"> Bachelor of Science in Computer Science </option>
			<option value="Bachelor of Science in Business Administration"> Bachelor of Science in Business Administration </option>
			<option value="Bachelor of Elementary Education"> Bachelor of Elementary Education </option>
			<option value="Bachelor of Secondary Education "> Bachelor of Secondary Education </option>
			<option value="Bachelor of Physical Education - Sports & Wellness Management"> Bachelor of Physical Education - Sports & Wellness Management </option>
			<option value="Bachelor of Physical Education"> Bachelor of Physical Education </option>
			<option value="Bachelor of Library and Information Science"> Bachelor of Library and Information Science </option>
			<option value="Teacher Certificate Program (TCP)"> Teacher Certificate Program (TCP) </option>
			<option value="Bachelor of Science in Nursing"> Bachelor of Science in Nursing </option>
			<option value="Bachelor of Science in Physical Therapy"> Bachelor of Science in Physical Therapy </option>
			<option value="Bachelor of Science in Radiologic Technology"> Bachelor of Science in Radiologic Technology </option>
			<option value="Bachelor of Science Medical Technology/ Medical Laboratory Science"> Bachelor of Science Medical Technology/ Medical Laboratory Science </option>
			<option value="Bachelor of Science in Pharmacy"> Bachelor of Science in Pharmacy </option>
			<option value="Bachelor of Science in Psychology"> Bachelor of Science in Psychology </option>
			<option value="Bachelor of Science in Midwifery"> Bachelor of Science in Midwifery </option>
			<option value="Bachelor of Science in Hospitality Management"> Bachelor of Science in Hospitality Management </option>
			<option value="Bachelor of Science in Tourism Management"> Bachelor of Science in Tourism Management </option>
			<?php foreach ($subjects_by_course as $course => $codes): ?>
                    <option value="<?php echo $course; ?>" <?php if ($subject['course'] == $course) echo 'selected'; ?>><?php echo $course; ?></option>
            <?php endforeach; ?>
		</select> <br><br> 

			Prerequisite 1: <select name="cmbprerequisite1" id="prerequisite1">
                <option value=""> None </option><br>
            </select><br>
            
            Prerequisite 2:<select name="cmbprerequisite2" id="prerequisite2">
            	<option value="">None</option><br>
                
            </select><br>
            
            Prerequisite 3:<select name="cmbprerequisite3" id="prerequisite3">
            	<option value="">None</option><br>
                
            </select><br>
	
	<button onclick="showAlert()" href = "subjects-management.php" name = "btnsubmit" value = "Update"> Update </button>
	<a href = "subjects-management.php"> Cancel </a>

	</form>
</center>
</div>

<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            updateDropdowns(); // Initial update on page load
        });

        document.getElementById('cmbcourse').addEventListener('change', updateDropdowns);

        function updateDropdowns() {
        	const selectedUnit = document.getElementById('cmbunit').value;
            const selectedCourse = document.getElementById('cmbcourse').value;
            const subjects = <?php echo json_encode($subjects_by_course); ?>;
            updatePrerequisiteOptions(subjects[selectedCourse] || [], 'prerequisite1', '<?php echo $subject['prerequisite1'] ?>');
            updatePrerequisiteOptions(subjects[selectedCourse] || [], 'prerequisite2', '<?php echo $subject['prerequisite2'] ?>');
            updatePrerequisiteOptions(subjects[selectedCourse] || [], 'prerequisite3', '<?php echo $subject['prerequisite3'] ?>');
        }

        function updatePrerequisiteOptions(options, fieldId, currentValue) {
            const select = document.getElementById(fieldId);
            select.innerHTML = '<option value="">None</option>';
            options.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;
                optionElement.selected = option === currentValue;
                select.appendChild(optionElement);
            });
        }
    </script>

</body>
</html>


