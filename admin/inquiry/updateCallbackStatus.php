<?php

include('../../config/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $id = $_POST['id'];
    $status = $_POST['status'];

  
    $validStatuses = ['Pending', 'In Progress', 'Failed', 'Completed'];
    if (!in_array($status, $validStatuses)) {

        $_SESSION['error'] = 'Invalid status selected.';
        header('Location: inquiries.php' . $id);
        exit();
    }


    $sql = "UPDATE callback_requests SET status = ?, updated_at = NOW() WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {

        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
          
            header("Location: viewCallback.php?id=$id&success=1");
            exit;
        } else {
      
            $_SESSION['error'] = 'Failed to update status.';
        }
        

        $stmt->close();
    } else {
        
        $_SESSION['error'] = 'Database query error.';
    }


    header('Location: inquiries.php');
    exit();
}
