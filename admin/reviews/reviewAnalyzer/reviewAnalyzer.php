<?php
include '../../../includes/phpInsight-master/autoload.php';
include '../../../config/dbconnect.php'; // Include your database connection

use PHPInsight\Sentiment;

// Create a new instance of Sentiment analysis class
$sentiment = new Sentiment();

// Fetch all reviews from the review table
$reviewsQuery = $conn->query("SELECT reviewID, reviewText FROM review");
if ($reviewsQuery->num_rows > 0) {
    // Prepare the INSERT statement for the review_sentiment table
    $insertStmt = $conn->prepare("
        INSERT INTO review_sentiment (reviewID, positive_score, negative_score, neutral_score, sentiment_category, last_analyzed)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        positive_score = VALUES(positive_score),
        negative_score = VALUES(negative_score),
        neutral_score = VALUES(neutral_score),
        sentiment_category = VALUES(sentiment_category),
        last_analyzed = NOW()
    ");

    // Loop through each review and analyze sentiment
    while ($review = $reviewsQuery->fetch_assoc()) {
        $reviewID = $review['reviewID'];
        $reviewText = $review['reviewText'];

        // Perform sentiment analysis
        $scores = $sentiment->score($reviewText);  // Get sentiment scores (positive, negative, neutral)
        $category = $sentiment->categorise($reviewText); // Get the sentiment category

        // Bind parameters and execute the insert query
        $insertStmt->bind_param('iddds', $reviewID, $scores['pos'], $scores['neg'], $scores['neu'], $category);
        $insertStmt->execute();
    }

    echo "Sentiment analysis completed and saved to review_sentiment table.";
} else {
    echo "No reviews found in the review table.";
}

// Close the statement and database connection
$insertStmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Analyzer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../resources/css/admin/reviews.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
   .sentiment-positive {
    background-color: rgba(72, 201, 72, 0.6); /* Darker green, more opaque */
    border: 1px solid rgba(72, 201, 72, 0.8); /* Stronger green border */
    backdrop-filter: blur(10px); /* Frosty glass effect */
    padding: 1.5rem;
    border-radius: 0.5rem;
}

.sentiment-negative {
    background-color: rgba(219, 112, 147, 0.6); /* Darker pink, more opaque */
    border: 1px solid rgba(219, 112, 147, 0.8); /* Stronger pink border */
    backdrop-filter: blur(10px); /* Frosty glass effect */
    padding: 1.5rem;
    border-radius: 0.5rem;
}



    </style>
</head>
<body class="bg-gray-100" style="background: url('../../../resources/images/bg3.jpg'); background-repeat: no-repeat; background-attachment: fixed; background-size: cover;">

    <!-- Navbar -->
    <?php include '../../../includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto my-10 p-5" >
        <div class="bg-white shadow-lg rounded-lg p-6" style="background-color: rgb(255,255,255, 0.8);">
            <h2 class="text-3xl font-bold text-gray-700 text-center mb-6">Review Analyzer</h2>

            <!-- Most Positive Reviews -->
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-green-600 mb-4">Most Positive Reviews</h3>
                <?php if (count($positiveReviews) > 0): ?>
                    <?php foreach ($positiveReviews as $review): ?>
                        <div class="p-5 mb-4 sentiment-positive rounded-md">
                            <h4 class="text-xl font-semibold text-blue-600">Review ID: <?php echo $review['reviewID']; ?> | Rating: <?php echo $review['rating']; ?></h4>
                            <p class="text-gray-700 mt-2"><?php echo $review['reviewText']; ?></p>
                            <p class="text-sm text-gray-500 mt-1">Positive Score: <?php echo $review['sentiment']['scores']['pos']; ?>, Negative Score: <?php echo $review['sentiment']['scores']['neg']; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">No positive reviews found.</p>
                <?php endif; ?>
            </div>

            <!-- Most Negative Reviews -->
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-red-600 mb-4">Most Negative Reviews</h3>
                <?php if (count($negativeReviews) > 0): ?>
                    <?php foreach ($negativeReviews as $review): ?>
                        <div class="p-5 mb-4 sentiment-negative rounded-md">
                            <h4 class="text-xl font-semibold text-red-600">Review ID: <?php echo $review['reviewID']; ?> | Rating: <?php echo $review['rating']; ?></h4>
                            <p class="text-gray-700 mt-2"><?php echo $review['reviewText']; ?></p>
                            <p class="text-sm text-gray-500 mt-1">Positive Score: <?php echo $review['sentiment']['scores']['pos']; ?>, Negative Score: <?php echo $review['sentiment']['scores']['neg']; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">No negative reviews found.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <?php include '../../../includes/footer.php'; ?>

</body>
</html>
