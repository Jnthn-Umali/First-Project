<?php
require_once "config.php";
include ("session-checker.php");
if(isset($_POST['btnsubmit']))
{
	$sql = "UPDATE tblstudents SET lastname = ?, firstname = ?, middlename = ?, course = ?, yearlevel= ? WHERE studentnumber = ?";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "ssssss", $_POST['txtlastname'], $_POST['txtfirstname'], $_POST['txtmiddlename'],  $_POST['cmbcourse'], $_POST['cmbyearlevel'], $_GET['studentnumber']);
		if(mysqli_stmt_execute($stmt))
		{
			$sql = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
			if($stmt = mysqli_prepare($link, $sql))
			{
				$date = date("m/d/Y");
				$time = date("h:i:s");
				$action = "Update";
				$module = "Student management";
				mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_GET['studentnumber'], $_SESSION['username']);
				if(mysqli_stmt_execute($stmt))
				{
					echo "<script>alert('Students record updated');</script>";
                    echo "<script>window.location.href='students-management.php';</script>";
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
			echo "<font color = 'red'> Error on updating student account. </font>";
		}
	}
}
else
{
	if(isset($_GET['studentnumber']) && !empty(trim($_GET['studentnumber'])))
	{
		$sql = "SELECT * FROM tblstudents WHERE studentnumber = ?";
		if($stmt = mysqli_prepare($link, $sql))
		{
			mysqli_stmt_bind_param($stmt, "s", $_GET['studentnumber']);
			if(mysqli_stmt_execute($stmt))
			{
				$result = mysqli_stmt_get_result($stmt);
				$account = mysqli_fetch_array($result, MYSQLI_ASSOC);
			}
			else
			{
				echo "<font color = 'red'> Error on loading the current student account values. </font>";
			}
		}
		else 
		{
           echo "<font color='red'>Error preparing select query.</font>";
        }
	}
}
?>

<html>
<title> Update student account - AU Subject Advising System - AUSMS </title>
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
    </header><br><br>
	<div class="wrapper"><br>
	<center>
	<h2> Change the value on this form to update the account. </h2><br>
	<form style="font-family:verdana;" action = "<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method = "POST">
	<input type = "text" placeholder = "Student no." name = "txtstudentnumber" value = "<?php echo $account['studentnumber']; ?>" readonly><br>
	 <input type = "text" placeholder = "Lastname" name = "txtlastname" value = "<?php echo $account['lastname']; ?>" readonly><br>
	 <input type = "text" placeholder = "Firstname" name = "txtfirstname" value = "<?php echo $account['firstname']; ?>" readonly><br> 
	 <input type = "text" placeholder = "Middlename" name = "txtmiddlename" value = "<?php echo $account['middlename']; ?>" readonly><br>
	 Course: <select name = "cmbcourse" id = "cmbcourse" required><br>
	 		<option value="">Current Course : <?php echo $account['course']; ?></option><br>
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
		</select> <br><br> 

	Year Level: <select name = "cmbyearlevel" id = "cmbyearlevel"  required><br>
			<option value="cmbyearlevel">Current Yearlevel : <?php echo $account['yearlevel']; ?></option><br>
			<option value="1st"> 1st </option>
			<option value="2nd"> 2nd  </option>
			<option value="3rd"> 3rd  </option>
			<option value="4th"> 4th  </option>
		</select> <br><br>

	
	<button onclick="showAlert()" href = "students-management.php" name = "btnsubmit" value = "Update"> Update </button>
	<a href = "students-management.php"> Cancel </a>

	</form>
</center>
</div>

 <footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>

</body>
</html>


