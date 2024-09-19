<?php
include('../../config/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ticketID = intval($_POST['ticketID']);
    $newStatus = $_POST['status'];


    $allowedStatuses = ['New', 'In Progress', 'Closed'];
    
    if (in_array($newStatus, $allowedStatuses)) {
    
        $sql = "UPDATE tickets SET ticketStatus = ?, updated_at = NOW() WHERE ticketID = ?";

      
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $newStatus, $ticketID);

    
            if ($stmt->execute()) {
    
                header("Location: viewInquiry.php?id=$ticketID&success=1");
                exit;
            } else {
      
                echo "Error updating status: " . $conn->error;
            }

            $stmt->close();
        } else {

            echo "Error preparing query: " . $conn->error;
        }
    } else {
 
        echo "Invalid status value!";
    }
} else {

    header("Location: inquiries.php");
    exit;
}

$conn->close();
?>
