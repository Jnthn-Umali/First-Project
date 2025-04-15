<?php
require_once "config.php";
include("session-checker.php");
if(isset($_POST['btnsubmit']))
{
	$sql = "SELECT * FROM tblaccounts WHERE username = ?";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "s", $_POST['txtusername']);
		if(mysqli_execute($stmt))
		{
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) == 0)
			{
				//create account
				$sql = "INSERT INTO tblaccounts (username, password, usertype, userstatus, createdby, datecreated) VALUES (?, ?, ?, ?, ?, ?)";
				if($stmt = mysqli_prepare($link, $sql))
				{
					$userstatus = "ACTIVE";
					$date = date("d/m/Y");
					mysqli_stmt_bind_param($stmt, "ssssss", $_POST['txtusername'], $_POST['txtpassword'], $_POST['cmbtype'], $userstatus, $_SESSION['username'], $date);

					if(mysqli_stmt_execute($stmt))
					{
						$sql = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
						if($stmt = mysqli_prepare($link, $sql))
						{
							$date = date("m/d/Y");
							$time = date("h:i:s");
							$action = "Create";
							$module = "Accounts management";
							mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, trim($_POST['txtusername']), $_SESSION['username']);
							if(mysqli_stmt_execute($stmt))
							{
								echo "<script>alert('User account created');</script>";
                echo "<script>window.location.href='account-management.php';</script>";
                exit();							}
							else
							{
								echo "<font color = 'red'> Error on inserting logs.</font>";
							}
						}
					}
					else
					{
						echo "<forn color = 'red'> Error on adding new account. </font>";
					}
				}
			}
			else
			{
				
				$error_message = "<center><br><br><br><br><br><br><br><br><font color = 'red'> Username is already in use.</font></center>";
				
			}
		}
		else
		{
			 $error_message = "<font color = 'red'>Error on checking if username is existing.</font>";
		}
	} 
}
?>

<html>
<title> Create new account - AU Subject Advising System - AUSMS</title>
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

    <?php if(!empty($error_message)): ?>
      <p><?php echo $error_message; ?></p>
    <?php endif; ?>
	<div class="wrapper"><br>
	<center>
	<h2> Fill up this form and submit to create a new account. </h2><br>

	<form style="font-family:verdana;" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
		Username: <input type = "text" name = "txtusername" required><br>
		Password: <input type = "password" name = "txtpassword" id="myInput" required>
		<input type="checkbox" onclick="myFunction()">Showpassword<br><br>
		Account type: <select name = "cmbtype" id = "cmbtype" required><br>
			<option value="">--Select Account type--</option><br>
			<option value="ADMINISTRATOR"> Administrator </option>
			<option value="REGISTRAR"> Registrar </option>
		</select> <br><br>

		<button type = "submit" name = "btnsubmit" value = "Submit"> Submit </button>
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
