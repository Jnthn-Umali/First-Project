<?php
require_once "config.php";
include ("session-checker.php");

if(isset($_POST['btnsubmit']))
{
	$sql = "UPDATE tblaccounts SET password = ?, userstatus = ? WHERE username = ?";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "sss", $_POST['txtpassword'], $_POST['rbstatus'], $_GET['username']);
		if(mysqli_stmt_execute($stmt))
		{



			$sql_user_type = "SELECT usertype FROM tblaccounts WHERE username = ?";
            if($stmt_user_type = mysqli_prepare($link, $sql_user_type)) {
                mysqli_stmt_bind_param($stmt_user_type, "s", $_GET['username']);
                if(mysqli_stmt_execute($stmt_user_type)) {
                    $result_user_type = mysqli_stmt_get_result($stmt_user_type);
                    $row_user_type = mysqli_fetch_assoc($result_user_type);
                    $user_type = $row_user_type['usertype'];
                    
                    // If the user type is STUDENT, update the password in tblstudents
                    if ($user_type === 'STUDENT') {
                        $sql_student = "UPDATE tblstudents SET password = ? WHERE studentnumber = ?";
                        if($stmt_student = mysqli_prepare($link, $sql_student)) {
                            mysqli_stmt_bind_param($stmt_student, "ss", $_POST['txtpassword'], $_GET['username']);
                            if(mysqli_stmt_execute($stmt_student)) {
                                // Password updated in tblstudents
                            } else {
                                echo "<font color='red'>Error in updating password in tblstudents: " . mysqli_error($link) . "</font>";
                            }
                        } else {
                            echo "<font color='red'>Error in preparing statement for tblstudents: " . mysqli_error($link) . "</font>";
                        }
                    }
                } else {
                    echo "<font color='red'>Error fetching user type: " . mysqli_error($link) . "</font>";
                }
            } else {
                echo "<font color='red'>Error preparing statement for fetching user type: " . mysqli_error($link) . "</font>";
            }
            
            // Proceed to logging
            $sql_log = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if($stmt_log = mysqli_prepare($link, $sql_log)){
                // Bind parameters for the log entry
                $date = date("m/d/Y"); // Use MySQL date format
                $time = date("h:i:s"); // Use MySQL time format
                $action = "Update";
                $module = "Accounts management";
                // Bind parameters and execute the log entry query
                mysqli_stmt_bind_param($stmt_log, "ssssss", $date, $time, $action, $module, $_GET['username'], $_SESSION['username']);
                mysqli_stmt_execute($stmt_log);
            } else {
                echo "<font color='red'>Error preparing statement for logging: " . mysqli_error($link) . "</font>";
            }
            
            echo "<script>alert('User account updated');</script>";
            echo "<script>window.location.href='account-management.php';</script>";
            exit();
        } else {
            // Error handling for account update
            echo "<font color='red'>Error in updating account: " . mysqli_error($link) . "</font>";
        }
    } else {
        echo "<font color='red'>Error preparing statement for updating account: " . mysqli_error($link) . "</font>";
    }
} else { // If form is not submitted, load the current values of the account
    if(isset($_GET['username']) && !empty(trim($_GET['username']))) {
        // Prepare and execute a query to fetch the current account details
        $sql = "SELECT * FROM tblaccounts WHERE username = ?";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
            if(mysqli_stmt_execute($stmt)) {
                // Fetch the result
                $result = mysqli_stmt_get_result($stmt);
                // Fetch the account details into an associative array
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else {
                // Error handling for fetching account details
                echo "<font color='red'>Error in loading the current account values: " . mysqli_error($link) . "</font>";
            }
        } else {
            echo "<font color='red'>Error preparing statement for fetching account details: " . mysqli_error($link) . "</font>";
        }
    }
}
?>

<html>
<title> Update account - AU Subject Advising System - AUSMS </title>
<head>
		<link rel="stylesheet" href="bootstrap.css">
    <style>
    	header {
            width: 100%;
			top: 0;
			right: 0;
			position: fixed;
			background: #829bed;
			box-shadow: 0px 2px 18px 0 rgb(129 162 182 / 20%);
			text-align: center;
			justify-content: space-between;
			padding: 5px 8%;
			transition: .3s
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
	<h2> Change the value on this form  to update the account. </h2><br>

	<form style="font-family:verdana;" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?username=' . $_GET['username']); ?>" method = "POST">
	Username: <input type = "text" name = "txtusername" value = "<?php echo $account['username']; ?>" readonly> <br>
	Password: <input type = "password" name = "txtpassword" id="myInput" value = "<?php echo $account['password']; ?>" required><br>
	<input type="checkbox" onclick="myFunction()">Showpassword<br><br>
	Current User type: <input type = "text" name = "txtusertype" value = "<?php echo $account['usertype']; ?>" readonly> <br>
	<?php if ($account['usertype'] !== 'STUDENT'): ?>
	Change User type to: <select name = "cmbtype" id = "cmbtype" required>
		<option value="">--Select User Type--</option><br>
		<option value="ADMINISTRATOR"> Administrator </option>
		<option value="REGISTRAR"> Registrar </option>
	</select><br>
	 <?php endif; ?>
	Current Status: <br> 
	<?php
		$userstatus = $account['userstatus'];
		if($userstatus == "ACTIVE")
		{
			?> 
			<input type = "radio" name = "rbstatus" value = "ACTIVE" checked>Active<br>
			<input type = "radio" name = "rbstatus" value = "INACTIVE" >Inactive<br> 
			<?php
		}
		else
		{
			?> <input type = "radio" name = "rbstatus" value = "ACTIVE" >Active<br>
			<input type = "radio" name = "rbstatus" value = "INACTIVE" checked>Inactive<br> <?php
		}
	?>
	<br><br>

	<button type = "submit" name = "btnsubmit" value = "Update"> Update </button>
	<a href = "account-management.php"> Cancel </a>

	</form>
</center>
</div>

<script>
function myFunction() {
  var x = document.getElementById("myInput");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>

</body>
</html>
 <footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>

