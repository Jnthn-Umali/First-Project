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
	<title>Student Management - Arellano University Subject Advising System - AUSAS</title>
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
        <a href="index.php"> Home </a>
        <?php if($show_accounts): ?>
		<a href="account-management.php"> Accounts </a>
		<?php endif; ?>
		<?php if($show_students): ?>
		<a class="active" href="#Students"> Students </a>
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
		<a class = 'btn btn-primary btn-sm' href = 'logout.php'>Logout</a> 
		</ul>
	
    </header><br><br><br><br>
  

<?php
	if(isset($_SESSION['usertype']))
	{
		
		echo "<br>";
		echo "<h1>Welcome, " . $_SESSION['username'] . "</h1>";
		echo "<h4>Account type: " . $_SESSION['usertype'] . "</h4>";

		echo "<center>";
		echo "<form action = " . htmlspecialchars($_SERVER['PHP_SELF']) . "  method = 'POST'>";
		echo "<a class = 'btn btn-primary btn-sm' href = 'create-student.php'>Create new student record</a><br><br>";
		echo "Search: <input type = 'text' name = 'txtsearch'>";
		echo "<input type = 'submit' name = 'btnsearch' value = 'Search'><br>";
		echo "</form>";
		echo "</center>";

		echo "<hr>";
		
	}
	else
	{
		header("location: login.php");
	}
?>
	
<?php
function buildTable($result)
{
	if(mysqli_num_rows($result) > 0)
	{
		echo "<table class='table'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>Studentnumber</th><th>Lastname</th><th>Firstname</th><th>Middlename</th><th>Course</th><th>Yearlevel</th><th>Created By</th><th>Date Created</th><th>Action</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<br>";

		while ($row = mysqli_fetch_array($result))
		{
			echo "<tbody>";
			echo "<tr>";
			echo "<td >" . $row['studentnumber'] . "</td>";
			echo "<td>" . $row['lastname'] . "</td>";
			echo "<td>" . $row['firstname'] . "</td>";
			echo "<td>" . $row['middlename'] . "</td>";
			echo "<td>" . $row['course'] . "</td>";
			echo "<td>" . $row['yearlevel'] . "</td>";
			echo "<td>" . $row['createdby'] . "</td>";
			echo "<td>" . $row['datecreated'] . "</td>";
			echo "<td>";
			echo "<a button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#exampleModalCenter' href = 'update-student.php?studentnumber=" . $row['studentnumber'] .  "'> Update </button>";
			echo "<a button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#exampleModalCenter' href='#' onclick='confirmAndDelete(\"" . $row['studentnumber'] . "\")'> Delete </button>";
			echo "</td>";
			echo "</tr>";
			echo "</tbody>";

			
		}
			echo "</table>";
			echo "<br>";

	}
		else
		{
			echo "No record/s found.";
		}
}
require_once "config.php";
if(isset($_POST['btnsearch']))
	{
		$sql = "SELECT * FROM tblstudents WHERE studentnumber LIKE ? OR lastname LIKE ? OR firstname LIKE ? OR middlename LIKE ? OR course LIKE ? OR yearlevel LIKE ? ORDER BY studentnumber";
	if($stmt = mysqli_prepare($link, $sql))
	{
		$searchvalue = '%' . $_POST['txtsearch'] . '%';
		mysqli_stmt_bind_param($stmt, "ssssss", $searchvalue, $searchvalue, $searchvalue, $searchvalue, $searchvalue, $searchvalue);

		if(mysqli_stmt_execute($stmt))
		{
			$result = mysqli_stmt_get_result($stmt);
			buildTable($result);
		}
	}
	else
	{
		echo "Error on search.";
	}
}
else
{
	$sql = "SELECT * FROM tblstudents ORDER BY studentnumber";
	if($stmt = mysqli_prepare($link, $sql))
	{
		if(mysqli_stmt_execute($stmt))
		{
			$result = mysqli_stmt_get_result($stmt);
			buildTable($result);
		}
	}
	else
	{
		echo "Error on students load.";
	}
}
?>

<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>

  <script>
        function confirmAndDelete(studentnumber) {
            if (confirm("Are you sure you want to delete this student record?")) {
                // Perform AJAX request to delete student record
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "delete-student.php?studentnumber=" + studentnumber, true);
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
            alert("Student record created");
            window.location.href = "students-management.php";
        }

        function showMessage() {
            alert("Student record updated");
            window.location.href = "students-management.php";
        }
    </script>
</body>
</html>
</center>
<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>