<?php
include 'config.php';  

if(isset($_POST['adminID'])){
    $adminID = $_POST['adminID'];

    $adminID = mysqli_real_escape_string($conn, $adminID);

    $query = "DELETE FROM `admin` WHERE `adminID` = '$adminID'";
    $query_run = mysqli_query($conn, $query);

    if($query_run){

        $_SESSION['message'] = "Admin removed successfully!";
        $_SESSION['message_type'] = 'success';
    } else {

        $_SESSION['message'] = "Failed to remove admin. Please try again.";
        $_SESSION['message_type'] = 'danger';
    }


    header("Location: adminPanel.php");
    exit();
} else {
    $_SESSION['message'] = "No admin ID provided!";
    $_SESSION['message_type'] = 'danger';
    header("Location: adminPanel.php");
    exit();
}
