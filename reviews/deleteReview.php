<?php
session_start();
include '../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reviewID = intval($_POST['reviewID']);
    $productID = intval($_POST['productID']);
    
    $userID = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT * FROM review WHERE reviewID = ? AND customerID = ? AND createdAt >= DATE_SUB(NOW(), INTERVAL 14 DAY)");
    $query->bind_param('ii', $reviewID, $userID);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM review WHERE reviewID = ?");
        $stmt->bind_param('i', $reviewID);
        
        if ($stmt->execute()) {
            $sentimentStmt = $conn->prepare("DELETE FROM review_sentiment WHERE reviewID = ?");
            $sentimentStmt->bind_param('i', $reviewID);
            
            if ($sentimentStmt->execute()) {
                $newUrl = $_SERVER['HTTP_REFERER'] ?? "../product/view_product.php?PRODUCTC=$productID";
                header("Location: $newUrl");
                exit();
            } else {
                echo "Error deleting sentiment data: " . $conn->error;
            }
        } else {
            echo "Error deleting review: " . $conn->error;
        }
    } else {
        echo "Unauthorized or review is too old to delete.";
    }
}
?>

