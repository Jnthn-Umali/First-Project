<?php
require_once "config.php";
include("session-checker.php");

if(isset($_GET['studentnumber'])) {
    $studentnumber = $_GET['studentnumber'];
    
    // Delete student record
    $sql_delete_student = "DELETE FROM tblstudents WHERE studentnumber = ?";
    if($stmt = mysqli_prepare($link, $sql_delete_student)) {
        mysqli_stmt_bind_param($stmt, "s", $studentnumber);
        if(mysqli_stmt_execute($stmt)) {
            // Delete account associated with the student
            $sql_delete_account = "DELETE FROM tblaccounts WHERE username = ?";
            if($stmt = mysqli_prepare($link, $sql_delete_account)) {
                mysqli_stmt_bind_param($stmt, "s", $studentnumber);
                if(mysqli_stmt_execute($stmt)) {
                    // Insert into tbllogs
                    $sql_logs = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                    if($stmt_logs = mysqli_prepare($link, $sql_logs)) {
                        $date = date("m/d/Y");
                        $time = date("h:i:s");
                        $action = "Delete";
                        $module = "Students management";
                        mysqli_stmt_bind_param($stmt_logs, "ssssss", $date, $time, $action, $module, $studentnumber, $_SESSION['username']);
                        if(mysqli_stmt_execute($stmt_logs)) {
                            echo "Student record deleted.";
                            exit();
                        } else {
                            echo "<font color='red'>Error on inserting logs</font>";
                        }
                    } else {
                        echo "<font color='red'>Error preparing logs query</font>";
                    }
                } else {
                    echo "<font color='red'>Error deleting student account</font>";
                }
            } else {
                echo "<font color='red'>Error preparing account deletion query</font>";
            }
        } else {
            echo "<font color='red'>Error deleting student record</font>";
        }
    } else {
        echo "<font color='red'>Error preparing student deletion query</font>";
    }
} else {
    echo "Invalid request.";
}
?>

