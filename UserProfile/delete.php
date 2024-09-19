<?php
session_start(); 

include 'config.php';

if(isset($_POST['customerID'])){
    $customerID = $_POST['customerID'];

    
    $query = "DELETE FROM `customer` WHERE `customerID` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customerID);

    
    if($stmt->execute()){
       
        $_SESSION['message'] = "Record deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting record: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }

   
    header("Location: RegisteredUsers.php"); 
    exit();
} else {
    echo "No customerID provided.";
}

$stmt->close();
$conn->close();
?>
