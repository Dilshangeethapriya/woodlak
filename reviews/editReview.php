<?php
session_start();
include '../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_edit'])) {
    $reviewID = intval($_POST['reviewID']);
    $productID = intval($_POST['productID']);
    $newReviewText = htmlspecialchars($_POST['reviewText']);
    $newRating = intval($_POST['rating']);
    
    // Check if review belongs to logged-in user and is editable
    $userID = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT * FROM review WHERE reviewID = ? AND customerID = ? AND createdAt >= DATE_SUB(NOW(), INTERVAL 14 DAY)");
    $query->bind_param('ii', $reviewID, $userID);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // User can edit this review
        $stmt = $conn->prepare("UPDATE review SET reviewText = ?,rating = ?  WHERE reviewID = ?");
        $stmt->bind_param('sii', $newReviewText, $newRating, $reviewID);
        if ($stmt->execute()) {
            header("Location: ../product/view_product.php?PRODUCTC=$productID");
            exit();
        } else {
            echo "Error updating review: " . $conn->error;
        }
    } else {
        echo "Unauthorized or review is too old to edit.";
    }
}
?>
