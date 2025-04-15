    <?php
require_once "config.php";
include("session-checker.php");

if(isset($_POST['username'])) {
    $username = $_POST['username'];
    
    // Delete account
    $sql_delete_account = "DELETE FROM tblaccounts WHERE username = ?";
    if($stmt = mysqli_prepare($link, $sql_delete_account)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        if(mysqli_stmt_execute($stmt)) {
            // Delete corresponding student record
            $sql_delete_student = "DELETE FROM tblstudents WHERE studentnumber = ?";
            if($stmt = mysqli_prepare($link, $sql_delete_student)) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                if(mysqli_stmt_execute($stmt)) {
                    // Insert into tbllogs
                    $sql_logs = "INSERT INTO tbllogs (datelog, timelog, action, module, ID, performedby) VALUES (?, ?, ?, ?, ?, ?)";
                    if($stmt_logs = mysqli_prepare($link, $sql_logs)) {
                        $date = date("m/d/Y");
                        $time = date("h:i:s");
                        $action = "Delete";
                        $module = "Accounts management";
                        mysqli_stmt_bind_param($stmt_logs, "ssssss", $date, $time, $action, $module, $username, $_SESSION['username']);
                        if(mysqli_stmt_execute($stmt_logs)) {
                            echo "User account and corresponding student record deleted.";
                            exit();
                        } else {
                            echo "Error on inserting logs";
                        }
                    } else {
                        echo "Error preparing logs query";
                    }
                } else {
                    echo "Error deleting corresponding student record";
                }
            } else {
                echo "Error preparing student deletion query";
            }
        } else {
            echo "Error deleting account";
        }
    } else {
        echo "Error preparing account deletion query";
    }
} else {
    echo "Invalid request.";
}
?>
