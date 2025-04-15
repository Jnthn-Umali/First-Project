<?php
require_once "config.php";
include("session-checker.php");
$error_message = "";

// Fetch all subjects and their courses for use in prerequisite dropdowns
$subjects = [];
$courses = [];

// Query to fetch all subjects
$sql = "SELECT code, course FROM tblsubjects ORDER BY course, code";
if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $subjects[$row['course']][] = $row['code'];
            if (!in_array($row['course'], $courses)) {
                $courses[] = $row['course'];
            }
        }
    }
    mysqli_stmt_close($stmt);
}

if(isset($_POST['btnsubmit']))
{
	$sql = "SELECT * FROM tblsubjects WHERE code = ?";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "s", $_POST['txtcode']);
		if(mysqli_execute($stmt))
		{
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) == 0)
			{
				//create student account
				$sql = "INSERT INTO tblsubjects (code, description, unit, course, prerequisite1, prerequisite2, prerequisite3, createdby, datecreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
				if($stmt = mysqli_prepare($link, $sql))
				{
					$userstatus = "ACTIVE";
					$date = date("d/m/Y");
					mysqli_stmt_bind_param($stmt, "sssssssss", $_POST['txtcode'], $_POST['txtdescription'], $_POST['cmbunit'], $_POST['cmbcourse'], $_POST['cmbprerequisite1'], $_POST['cmbprerequisite2'], $_POST['cmbprerequisite3'], $_SESSION['username'], $date);

					if(mysqli_stmt_execute($stmt))
					{
						$sql = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
						if($stmt = mysqli_prepare($link, $sql))
						{
							$date = date("m/d/Y");
							$time = date("h:i:s");
							$action = "Create";
							$module = "Subjects management";
							mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_POST['txtcode'], $_SESSION['username']);
							if(mysqli_stmt_execute($stmt))
							{
								echo "<script>alert('Subject Created');</script>";
                                echo "<script>window.location.href='subjects-management.php';</script>";
                                exit();
							}
							else
							{
								echo "<font color = 'red'> Error on inserting logs.</font>";
							}
						}
					}
					else
					{
						echo "<font color = 'red'> Error on adding new subject. </font>";
					}
				}
			}
			else
			{
				echo "<center>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<font color = 'red'> Code is already in use.</font>";
				echo "</center>";

			}
		}
		else
		{
			echo "<font color = 'red'> Error on checking if code is existing.</font>";
		}
	} 
}
?>
<!DOCTYPE html>
<head>
		<meta charset="UTF-8">
		<title> Create new subject - AU Subject Advising System - AUSMS</title>
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
    </header><br><br><br>
	<center>
	<div class="wrapper"><br>
	<?php if(!empty($error_message)): ?>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
	<h1> Fill up this form and submit to create a new subject. </h1><br>
	
	
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
		<input type = "text" placeholder = "Code" name = "txtcode" required><br>
		<input type = "text" placeholder = "Description" name = "txtdescription" required><br>
		Unit: <br><select name = "cmbunit" id = "cmbunit" required><br>
		<option value="">--Select Unit--</option><br>
			<option value="1"> 1  </option>
			<option value="2"> 2 </option>
			<option value="3"> 3 </option>
			<option value="4"> 4 </option>
			<option value="5"> 5 </option>
		</select> <br><br>
		Course: <select name = "cmbcourse" id = "cmbcourse" required><br>
			<option value="">--Select Course--</option><br>
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
			<?php foreach ($courses as $course): ?>
                    <option value="<?= $course ?>"><?= $course ?></option>
                <?php endforeach; ?>
		</select> <br><br>

            Prerequisite 1: <select name="cmbprerequisite1" id="prerequisite1">
                <option value="">None</option>
            </select><br>
            
            Prerequisite 2:<select name="cmbprerequisite2" id="prerequisite2">
                <option value="">None</option>
            </select><br>
            
            Prerequisite 3:<select name="cmbprerequisite3" id="prerequisite3">
                <option value="">None</option>
            </select><br>

		<button type = "submit" name = "btnsubmit" value = "Submit"> Submit </button><br>
		<a href = "subjects-management.php"> Cancel </a>
	
	</form>
</div>
</center>

<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>

<script>
        document.getElementById('cmbcourse').addEventListener('change', function() {
            const selectedCourse = this.value;
            const subjects = <?php echo json_encode($subjects); ?>;
            updatePrerequisiteOptions(subjects[selectedCourse] || [], 'prerequisite1');
            updatePrerequisiteOptions(subjects[selectedCourse] || [], 'prerequisite2');
            updatePrerequisiteOptions(subjects[selectedCourse] || [], 'prerequisite3');
        });

        function updatePrerequisiteOptions(options, fieldId) {
            const select = document.getElementById(fieldId);
            select.innerHTML = '<option value="">None</option>';
            options.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;
                select.appendChild(optionElement);
            });
        }
</script>

</body>
</html>
<br><br>