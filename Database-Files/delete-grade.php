<?php
require_once "config.php";
include("session-checker.php");

// Check if code parameter is set in the GET request
if(isset($_GET['code'])) {
    $code = $_GET['code'];
    $student_number = $_SESSION['student_info']['studentnumber'];
    // Prepare and execute the delete query
    $sql = "DELETE FROM tblgrades WHERE code = ? AND studentnumber = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind parameters and execute the delete query
        mysqli_stmt_bind_param($stmt, "ss", $code, $student_number);
        if (mysqli_stmt_execute($stmt)) {
            // If deletion is successful, insert a log entry
            $date = date("m/d/Y");
            $time = date("h:i:s");
            $action = "Delete";
            $module = "Grades management";
            $performed_by = $_SESSION['username'];

            $sql_logs = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt_logs = mysqli_prepare($link, $sql_logs)) {
                mysqli_stmt_bind_param($stmt_logs, "ssssss", $date, $time, $action, $module, $student_number
, $performed_by);
                if (mysqli_stmt_execute($stmt_logs)) {
                    // If log insertion is successful, redirect to grades management page
                    echo "Grade deleted.";
                    exit();
                } else {
                    echo "Error: Failed to insert log entry.";
                }
            } else {
                echo "Error: Failed to prepare log insertion query.";
            }
        } else {
            echo "Error: Failed to delete grade.";
        }
    } else {
        echo "Error: Failed to prepare delete query.";
    }
} else {
    echo "Error: Invalid request.";
}
?>
