<?php

include('../../config/dbconnect.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ticketID'])) {
    $ticketID = intval($_POST['ticketID']);  

  
    $query = "SELECT * FROM tickets WHERE ticketID = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $ticketID);  
        $stmt->execute();  
        $result = $stmt->get_result(); 
        
        
        if ($result->num_rows > 0) {
           
            $sql = "DELETE FROM tickets WHERE ticketID = ?";
            
            if ($deleteStmt = $conn->prepare($sql)) {
                $deleteStmt->bind_param("i", $ticketID);  
                $deleteStmt->execute();  
                
                
                header("Location: inquiries.php?message=Inquiry deleted successfully.");
                exit;
            } else {
                
                echo "Error: Unable to delete the inquiry.";
                exit;
            }
        } else {

            header("Location: inquiries.php?error=Inquiry not found.");
            exit;
        }
        $stmt->close();  
    } else {
        echo "Error: Unable to find the inquiry.";
        exit;
    }
} else {

    header("Location: inquiries.php");
    exit;
}

$conn->close();
?>
