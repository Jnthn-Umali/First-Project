<?php
require_once "config.php";
include("session-checker.php");
if(isset($_GET['code'])) 
{
    $code = $_GET['code'];
	$sql = "DELETE FROM tblsubjects WHERE code = ?";
	if($stmt = mysqli_prepare($link, $sql))
	{
		mysqli_stmt_bind_param($stmt, "s", $code);
		if(mysqli_execute($stmt))
		{

			$sql = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
			if($stmt = mysqli_prepare($link, $sql))
			{
				$date = date("m/d/Y");
				$time = date("h:i:s");
				$action = "Delete";
				$module = "Subject management";
				mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $code, ($_SESSION['username']));
				if(mysqli_stmt_execute($stmt))
				{
					echo "Subject deleted";
					
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
			echo "<font color = 'red'> Error on deleting subject.</font>";
		}
	}
}
else {
    echo "Invalid request.";
}
?>

