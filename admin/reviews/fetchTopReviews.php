<?php
include '../../config/dbconnect.php';

// Get the selected product ID from the request, or set it to null if no product is selected
$productID = isset($_POST['productID']) ? intval($_POST['productID']) : null;

$mostPositiveReviewsURL = $productID ? "mostPositiveReviews.php?productID={$productID}" : "mostPositiveReviews.php";
$mostNegativeReviewsURL = $productID ? "mostNegativeReviews.php?productID={$productID}" : "mostNegativeReviews.php";

// Fetch most positive review
if ($productID) {
    // Fetch most positive review for a specific product
    $mostPositiveQuery = $conn->prepare("
        SELECT rs.*, r.reviewText, r.customerName, r.rating 
        FROM review_sentiment rs
        JOIN review r ON rs.reviewID = r.reviewID
        WHERE r.productID = ? 
        ORDER BY rs.positive_score DESC
        LIMIT 1
    ");
    $mostPositiveQuery->bind_param('i', $productID);
} else {
    // Fetch the most positive review across all products
    $mostPositiveQuery = $conn->prepare("
        SELECT rs.*, r.reviewText, r.customerName, r.rating 
        FROM review_sentiment rs
        JOIN review r ON rs.reviewID = r.reviewID
        ORDER BY rs.positive_score DESC
        LIMIT 1
    ");
}
$mostPositiveQuery->execute();
$mostPositiveReview = $mostPositiveQuery->get_result()->fetch_assoc();

// Fetch most negative review
if ($productID) {
    // Fetch most negative review for a specific product
    $mostNegativeQuery = $conn->prepare("
        SELECT rs.*, r.reviewText, r.customerName, r.rating 
        FROM review_sentiment rs
        JOIN review r ON rs.reviewID = r.reviewID
        WHERE r.productID = ? 
        ORDER BY rs.negative_score DESC
        LIMIT 1
    ");
    $mostNegativeQuery->bind_param('i', $productID);
} else {
    // Fetch the most negative review across all products
    $mostNegativeQuery = $conn->prepare("
        SELECT rs.*, r.reviewText, r.customerName, r.rating 
        FROM review_sentiment rs
        JOIN review r ON rs.reviewID = r.reviewID
        ORDER BY rs.negative_score DESC
        LIMIT 1
    ");
}
$mostNegativeQuery->execute();
$mostNegativeReview = $mostNegativeQuery->get_result()->fetch_assoc();

// Function to fetch replies for a review
function fetchReplies($conn, $reviewID) {
    $repliesQuery = $conn->prepare("SELECT * FROM reviewreply WHERE reviewID = ? ORDER BY createdAt ASC");
    $repliesQuery->bind_param('i', $reviewID);
    $repliesQuery->execute();
    $repliesResult = $repliesQuery->get_result();

    $repliesHTML = '';
    if ($repliesResult->num_rows > 0) {
        while ($reply = $repliesResult->fetch_assoc()) {
            $repliesHTML .= "<div class='reply bg-[#1f2937] p-3 rounded-lg mt-2'>
                                <p class='text-white'><b>" . htmlspecialchars($reply['userName']) . ":</b> " . htmlspecialchars($reply['replyText']) . "</p>
                             </div>";
        }
    } else {
        $repliesHTML = "<p class='text-sm text-gray-400'>No replies yet.</p>";
    }
    return $repliesHTML;
}

// Fetch replies for the most positive review
$positiveRepliesHTML = $mostPositiveReview ? fetchReplies($conn, $mostPositiveReview['reviewID']) : '';

// Fetch replies for the most negative review
$negativeRepliesHTML = $mostNegativeReview ? fetchReplies($conn, $mostNegativeReview['reviewID']) : '';

// HTML for the most positive review with reply form
$mostPositiveHTML = $mostPositiveReview ? 
    "<div class='bg-[#111827] p-4 mb-6 rounded-lg shadow-lg'>
        <h3 class='text-2xl text-green-500 font-bold'>Most Positive Review</h3>
        <a href='$mostPositiveReviewsURL' class='text-sm text-white hover:underline mb-6'>
            Most Positive Reviews &#10093;&#10093;
            </a>
        <p class='text-lg text-[#C4A484]'><strong></strong> {$mostPositiveReview['customerName']}</p>
        <p class='text-white'><strong></strong> &#11088;{$mostPositiveReview['rating']}</p>
        <p class='text-white'> {$mostPositiveReview['reviewText']}</p>
        <button onclick=\"toggleVisibility('positiveRepliesForm')\" class='bg-transparent hover:text-white hover:underline text-[#C4A484] rounded px-3 py-1 mt-2'>Show/Hide Replies</button>
        <div id='positiveRepliesForm' style='display:none; margin-top: 10px;'>
            <div>$positiveRepliesHTML</div>
            <form action='' method='POST' class='mt-3'>
                <input type='hidden' name='reviewID' value='{$mostPositiveReview['reviewID']}'>
                <div>
                    <textarea name='replyText' rows='2' class='block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm' placeholder='Write a reply...' required></textarea>
                </div>
                <div class='text-left mt-2'>
                    <button type='submit' name='submit_reply' class='bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2 transition duration-150 ease-in-out'>
                        Reply
                    </button>
                </div>
            </form>
        </div>
    </div>"
    : "<div >
       
    </div>";

// HTML for the most negative review with reply form
$mostNegativeHTML = $mostNegativeReview ? 
    "<div class='bg-[#111827] p-4 mb-6 rounded-lg shadow-lg'>
        <h3 class='text-2xl text-red-500 font-bold'>Most Negative Review</h3>
         <a href='$mostNegativeReviewsURL' class='text-sm text-white hover:underline mb-6'>
            Most Negative Reviews &#10093;&#10093;
         </a>
        <p class='text-lg text-[#C4A484]'> {$mostNegativeReview['customerName']}</p>
        <p class=' text-white'>&#11088;{$mostNegativeReview['rating']}</p>
        <p class='text-white'> {$mostNegativeReview['reviewText']}</p>
        <button onclick=\"toggleVisibility('negativeRepliesForm')\" class='bg-transparent hover:text-white hover:underline text-[#C4A484] rounded px-3 py-1 mt-2'>Show/Hide Replies</button>
        <div id='negativeRepliesForm' style='display:none; margin-top: 10px;'>
            <div>$negativeRepliesHTML</div>
            <form action='' method='POST' class='mt-3'>
                <input type='hidden' name='reviewID' value='{$mostNegativeReview['reviewID']}'>
                <div>
                    <textarea name='replyText' rows='2' class='block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm' placeholder='Write a reply...' required></textarea>
                </div>
                <div class='text-left mt-2'>
                    <button type='submit' name='submit_reply' class='bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2 transition duration-150 ease-in-out'>
                        Reply
                    </button>
                </div>
            </form>
        </div>
    </div>"
    : "<div>
    </div>";

// Return the HTML as a JSON response
echo json_encode([
    'mostPositive' => $mostPositiveHTML,
    'mostNegative' => $mostNegativeHTML
]);
?>
