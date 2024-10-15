<?php
session_start();
include '../config/dbconnect.php';
include '../includes/phpInsight-master/autoload.php'; // Include sentiment analysis library

use PHPInsight\Sentiment;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_edit'])) {
    $reviewID = intval($_POST['reviewID']);
    $productID = intval($_POST['productID']);
    $newReviewText = $_POST['reviewText']; // No need for htmlspecialchars before DB storage
    $newRating = intval($_POST['rating']);
    
    // Check if review belongs to logged-in user and is editable (within 14 days)
    $userID = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT * FROM review WHERE reviewID = ? AND customerID = ? AND createdAt >= DATE_SUB(NOW(), INTERVAL 14 DAY)");
    $query->bind_param('ii', $reviewID, $userID);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // User can edit this review
        $stmt = $conn->prepare("UPDATE review SET reviewText = ?, rating = ? WHERE reviewID = ?");
        $stmt->bind_param('sii', $newReviewText, $newRating, $reviewID);
        
        if ($stmt->execute()) {
            // After updating the review, analyze the sentiment of the updated review text
            $sentiment = new Sentiment();
            $scores = $sentiment->score($newReviewText); // Calculate sentiment scores
            $category = $sentiment->categorise($newReviewText); // Determine sentiment category
            
            // Update the review_sentiment table
            $stmtSentiment = $conn->prepare("
                UPDATE review_sentiment 
                SET positive_score = ?, negative_score = ?, neutral_score = ?, sentiment_category = ?, last_analyzed = NOW() 
                WHERE reviewID = ?
            ");
            $stmtSentiment->bind_param(
                'dddsi',
                $scores['pos'], 
                $scores['neg'], 
                $scores['neu'], 
                $category, 
                $reviewID
            );

            if ($stmtSentiment->execute()) {
                $newUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../product/view_product.php?PRODUCTC=$productID";
                header("Location: $newUrl");
                exit();
            } else {
                echo "Error updating sentiment analysis: " . $conn->error;
            }
        } else {
            echo "Error updating review: " . $conn->error;
        }
    } else {
        echo "Unauthorized or review is too old to edit.";
    }
}
?>
