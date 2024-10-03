<?php
include '../config/dbconnect.php'; 

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $productID = intval($_REQUEST["PRODUCTC"]);
    $customerID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $customerName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $_POST['customerName'];
    $rating = $_POST['rating'];
    $reviewText = $_POST['reviewText'];

    // Sanitize input
    $customerName = htmlspecialchars($customerName);
    $reviewText = htmlspecialchars($reviewText);

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO review (productID, customerID, customerName, rating, reviewText) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iisis', $productID, $customerID, $customerName, $rating, $reviewText);
    
    if ($stmt->execute()) {
        // Redirect to the same product page after submission
        header("Location: view_product.php?PRODUCTC=$productID");
        exit();
    } else {
        echo "Error submitting review: " . $conn->error;
    }
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $reviewID = intval($_POST['reviewID']);
    $replyText = htmlspecialchars($_POST['replyText']); // Sanitize reply text

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO reviewreply (reviewID, replyText) VALUES (?, ?)");
    $stmt->bind_param('is', $reviewID, $replyText);

    if ($stmt->execute()) {
        $productID = intval($_REQUEST["PRODUCTC"]);
        header("Location: view_product.php?PRODUCTC=$productID");
        exit();
    } else {
        echo "Error submitting reply: " . $conn->error;
    }
}

// Code to fetch product details, reviews, and render HTML
$productID = intval($_REQUEST["PRODUCTC"]); 

// Fetch the latest 10 reviews
$reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY createdAt DESC LIMIT 10");
$reviewsQuery->bind_param('i', $productID);
$reviewsQuery->execute();
$reviews = $reviewsQuery->get_result();

if ($reviews === false) {
    echo "Error in fetching reviews: " . $conn->error;
    exit();
}

?>

<!-- HTML and review display goes here -->

<div class="review-section my-36 max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(220, 255, 220, 0.8);">
    <h2 class="text-[#C4A484] text-4xl mb-4 text-center">Product Reviews</h2>

    <?php include "ratingCounts.php" ?>

    <!-- Button to toggle the review form -->
    <button onclick="toggleVisibility('reviewForm')" class="bg-[#78350f] hover:bg-[#5a2b09] text-white rounded px-4 py-2 mb-4">Leave a Review</button>
    
    <!-- Review form, hidden by default -->
    <div id="reviewForm" style="display:none;" class="mx-auto bg-[#1f2937] p-6 rounded-lg shadow-lg">
        <form action="" method="POST" class="space-y-4">
            <?php if (!isset($_SESSION['user_name'])): ?>
                <div>
                    <label for="customerName" class="block text-white text-sm font-medium">Name:</label>
                    <input type="text" name="customerName" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-700">Reviewing as: <span class="font-bold"><?php echo $_SESSION['user_name']; ?></span></p>
            <?php endif; ?>

            <div>
                <label for="rating" class="block text-sm font-medium text-white">Rating:</label>
                <select name="rating" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Terrible</option>
                </select>
            </div>

            <div>
                <label for="reviewText" class="block text-sm font-medium text-white">Review:</label>
                <textarea name="reviewText" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" required></textarea>
            </div>

            <div class="text-left">
                <button type="submit" name="submit_review" class="bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2 transition duration-150 ease-in-out">
                    Submit Review
                </button>
            </div>
        </form>
    </div>

    <hr class="my-12 h-0.5 border-t-0 bg-[#C4A484]" />

    <!-- Display the latest 10 reviews -->
    <h2 class="text-[#C4A484] text-2xl my-5">Customer Reviews</h2>
    
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="review bg-[#1f2937] p-4 rounded-lg mb-4">
                <h4 class="text-[#C4A484] text-lg"><b><?php echo htmlspecialchars($review['customerName']); ?></b></h4>
                <div class="flex items-center">
                    <p class="ms-2 text-sm font-bold text-gray-900 dark:text-white"> &#11088; <?php echo $review['rating']; ?></p>
                </div>
                <p class="text-white"><?php echo htmlspecialchars($review['reviewText']); ?></p>
                <p><small class="text-gray-400"><?php echo $review['createdAt']; ?></small></p>

                <!-- Button to toggle replies -->
                <button onclick="toggleVisibility('replies_<?php echo $review['reviewID']; ?>')" class="bg-transparent hover:text-white hover:underline text-[#C4A484] rounded px-3 py-1 mt-2">Show/Hide Replies</button>

                <!-- Display replies, hidden by default -->
                <div id="replies_<?php echo $review['reviewID']; ?>" style="display:none; margin-top: 10px;">
                    <?php
                    $reviewID = $review['reviewID'];
                    $replies = $conn->query("SELECT * FROM reviewreply WHERE reviewID = $reviewID ORDER BY createdAt ASC");

                    if ($replies->num_rows > 0) {
                        while ($reply = $replies->fetch_assoc()) {
                            echo "<div class='reply bg-[#111827] p-3 rounded-lg mt-2'>
                                    <p class='text-white'><b>Reply:</b> " . htmlspecialchars($reply['replyText']) . "</p>
                                  </div>";
                        }
                    } else {
                        echo "<p class='text-sm text-gray-400'>No replies yet.</p>";
                    }
                    ?>

                    <!-- Reply form -->
                    <form action="" method="POST" class="mt-3">
                        <input type="hidden" name="reviewID" value="<?php echo $review['reviewID']; ?>">
                        <div>
                            <textarea name="replyText" rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Write a reply..." required></textarea>
                        </div>
                        <div class="text-left mt-2">
                            <button type="submit" name="submit_reply" class="bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2 transition duration-150 ease-in-out">
                                Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-700">No reviews available yet. Be the first one to leave a review!</p>
    <?php endif;
    if($reviews->num_rows >=10) : ?>
        <div class="text-center">
        <a href="../reviews/allReviews.php?PRODUCTC=<?php echo $productID; ?>" class="text-[#C4A484] text-lg hover:underline">View All Reviews &#10093;&#10093; </a>
    </div>

    <?php endif; ?>
    

</div>

<script>
    function toggleVisibility(id) {
        var element = document.getElementById(id);
        if (element.style.display === 'none') {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    }
</script>

<?php
$conn->close();
?>

