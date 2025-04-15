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
$show_grades = true;
$show_mygrades = false;
$show_logs = true;
$show_about = true;

// Update visibility flags based on user type
if($usertype == "REGISTRAR") {
    $show_accounts = false;
    $show_grades = false;
} elseif($usertype == "STUDENT") {
    $show_accounts = false;
    $show_students = false;
    $show_subjects = false;
    $show_grades = false;
    $show_mygrades = true;
    $show_logs = false;
    $show_about = false;
}
?>

<?php
require_once "config.php";
include("session-checker.php");

// Fetch current user's password
$current_password = "";
$sql_current_password = "SELECT password FROM tblaccounts WHERE username = ?";
if($stmt_current_password = mysqli_prepare($link, $sql_current_password)) {
    mysqli_stmt_bind_param($stmt_current_password, "s", $_SESSION['username']);
    if(mysqli_stmt_execute($stmt_current_password)) {
        $result_current_password = mysqli_stmt_get_result($stmt_current_password);
        $row_current_password = mysqli_fetch_assoc($result_current_password);
        $current_password = $row_current_password['password'];
    } else {
        echo "<font color='red'>Error: Unable to fetch current password from tblaccounts.</font>";
    }
} else {
    echo "<font color='red'>Error: Unable to prepare SQL statement to fetch current password from tblaccounts.</font>";
}

// Check if the form is submitted
if(isset($_POST['btnsubmit'])) {
    // Update password in tblaccounts
    $new_password = $_POST['txtpassword']; // Store the new password as it is
    $sql_update_password = "UPDATE tblaccounts SET password = ? WHERE username = ?";
    if($stmt_update_password = mysqli_prepare($link, $sql_update_password)) {
        mysqli_stmt_bind_param($stmt_update_password, "ss", $new_password, $_SESSION['username']);
        if(mysqli_stmt_execute($stmt_update_password)) {
            // Update password in tblstudents
            $sql_update_password_students = "UPDATE tblstudents SET password = ? WHERE studentnumber = ?";
            if($stmt_update_password_students = mysqli_prepare($link, $sql_update_password_students)) {
                mysqli_stmt_bind_param($stmt_update_password_students, "ss", $new_password, $_SESSION['username']);
                if(mysqli_stmt_execute($stmt_update_password_students)) {
                    // Insert log into tbllogs
                    $sql_logs = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                    if($stmt_logs = mysqli_prepare($link, $sql_logs)) {
                        $date = date("m/d/Y");
                        $time = date("h:i:s");
                        $action = "Change";
                        $module = "Change Password";
                        mysqli_stmt_bind_param($stmt_logs, "ssssss", $date, $time, $action, $module, $_SESSION['username'], $_SESSION['username']);
                        if(mysqli_stmt_execute($stmt_logs)) {
                            echo "<script>alert('Password Changed Successfully');</script>";
                            echo "<script>window.location.href='index.php';</script>";
                            // Redirect to some page after successful update
                            exit();
                        } else {
                            echo "<font color='red'>Error on inserting logs.</font>";
                        }
                    } else {
                        echo "<font color='red'>Error: Unable to prepare SQL statement for logging.</font>";
                    }
                } else {
                    echo "<font color='red'>Error: Unable to update password in tblstudents.</font>";
                }
            } else {
                echo "<font color='red'>Error: Unable to prepare SQL statement to update password in tblstudents.</font>";
            }
        } else {
            echo "<font color='red'>Error: Unable to update password in tblaccounts.</font>";
        }
    } else {
        echo "<font color='red'>Error: Unable to prepare SQL statement to update password in tblaccounts.</font>";
    }
}
?>

<!DOCTYPE html>
<head>
        <meta charset="UTF-8">
        <title> Change Password - AU Subject Advising System - AUSMS</title>
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

      <ul class="navbar">
        <a href="index.php"> Home </a>
        <?php if($show_mygrades): ?>
        <a href="my-grades.php"> MyGrades </a>
        <?php endif; ?>
        <a class = 'btn btn-primary btn-sm' href="change-password.php" > Change Password </a>
        <a class = 'btn btn-primary btn-sm' href = 'logout.php'>Logout</a> 
        </ul>
    </header><br><br><br><br>

    <center>
    <div class="wrapper"><br>
    <h1> Fill up this form to change password. </h1><br>
    
    
    <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
        Current Password: <input type="password" id="currentPassword" value="<?php echo $current_password; ?>" readonly><br>
        <input type="checkbox" onclick="toggleCurrentPassword()"><br>Show Password<br><br>
        
        New Password: <input type = "password" id="newPassword" name = "txtpassword" required>
        <input type="checkbox" onclick="toggleNewPassword()"><br>Show Password<br><br>

        <button type = "submit" name = "btnsubmit" value = "Submit"> Submit </button><br>
        <a href="index.php">Cancel</a>
    
    </form>
</div>
<footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>

<script>
function toggleCurrentPassword() {
  var x = document.getElementById("currentPassword");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
function toggleNewPassword() {
  var x = document.getElementById("newPassword");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}

</script>


</center>
</body>
</html>
