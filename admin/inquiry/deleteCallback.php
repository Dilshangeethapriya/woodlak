<?php

include('../../config/dbconnect.php');  

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {

    $id = intval($_POST['id']);  


    $query = "SELECT * FROM callback_requests WHERE id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);  
        $stmt->execute();  
        $result = $stmt->get_result(); 

      
        if ($result->num_rows > 0) {
        
            $sql = "DELETE FROM callback_requests WHERE id = ?";
            
            if ($deleteStmt = $conn->prepare($sql)) {
                $deleteStmt->bind_param("i", $id);  
                $deleteStmt->execute();  
                
   
                header("Location: inquiries.php?message=Callback request deleted successfully.");
                exit;
            } else {
              
                echo "Error: Unable to delete the callback request.";
                exit;
            }
        } else {
           
            header("Location: inquiries.php?error=Callback request not found.");
            exit;
        }
        $stmt->close();  
    } else {
        echo "Error: Unable to find the callback request.";
        exit;
    }
} else {

    header("Location: inquiries.php");
    exit;
}

$conn->close();
?>
