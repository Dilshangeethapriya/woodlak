<?php
include '../../config/dbconnect.php';

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

// Define reviews per page and get current page number
$reviewsPerPage = 10;
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($currentPage - 1) * $reviewsPerPage;

// Get the productID from the URL, if present
$productID = isset($_GET['productID']) ? intval($_GET['productID']) : null;

// Fetch most positive reviews with pagination
if ($productID) {
    // Fetch most positive reviews for a specific product
    $mostPositiveQuery = $conn->prepare("
        SELECT rs.*, r.reviewID, r.reviewText, r.customerName, r.rating, r.createdAt
        FROM review_sentiment rs
        JOIN review r ON rs.reviewID = r.reviewID
        WHERE r.productID = ?
        ORDER BY rs.positive_score DESC
        LIMIT ?, ?
    ");
    $mostPositiveQuery->bind_param('iii', $productID, $offset, $reviewsPerPage);
} else {
    // Fetch most positive reviews across all products
    $mostPositiveQuery = $conn->prepare("
        SELECT rs.*, r.reviewID, r.reviewText, r.customerName, r.rating, r.createdAt 
        FROM review_sentiment rs
        JOIN review r ON rs.reviewID = r.reviewID
        ORDER BY rs.positive_score DESC
        LIMIT ?, ?
    ");
    $mostPositiveQuery->bind_param('ii', $offset, $reviewsPerPage);
}

$mostPositiveQuery->execute();
$mostPositiveReviews = $mostPositiveQuery->get_result();

// Get total number of most positive reviews for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM review_sentiment rs JOIN review r ON rs.reviewID = r.reviewID";
if ($productID) {
    $totalQuery .= " WHERE r.productID = $productID";
}
$totalResult = $conn->query($totalQuery);
$totalReviews = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalReviews / $reviewsPerPage);

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $reviewID = intval($_POST['reviewID']);
    $replyText = htmlspecialchars($_POST['replyText']); // Sanitize reply text
    $userName = "Admin";
    
    $stmt = $conn->prepare("INSERT INTO reviewreply (reviewID, userName, replyText) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $reviewID, $userName, $replyText);

    if ($stmt->execute()) {
        header("Location: mostPositiveReviews.php?page=$currentPage&productID=$productID");
        exit();
    } else {
        echo "Error submitting reply: " . $conn->error;
    }
}

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Positive Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../resources/css/admin/reviews.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Add jQuery for Ajax -->
</head>
<body>
<?php include '../../includes/adminNavbar.php' ?>

<div class="review-section mt-10 mb-36 max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(255, 255, 255, 0.8);">
    <h2 class="text-[#9b734b] text-4xl mb-6 text-center">Most Positive Reviews</h2>

    <div id="reviewsSection">
        <?php if ($mostPositiveReviews->num_rows > 0): ?>
            <?php while ($review = $mostPositiveReviews->fetch_assoc()): ?>
                <div class="review bg-[#111827] p-4 rounded-lg mb-4">
                    <h4 class="text-[#C4A484] text-lg"><b><?php echo htmlspecialchars($review['customerName']); ?></b></h4>
                    <div class="flex items-center">
                        <p class="ms-2 text-sm font-bold text-gray-900 dark:text-white"> &#11088; <?php echo $review['rating']; ?></p>
                    </div>
                    <p class="text-white"><?php echo htmlspecialchars(html_entity_decode($review['reviewText'])); ?></p>
                    <p><small class="text-gray-400"><?php echo $review['createdAt']; ?></small></p>

                    <!-- Button to toggle replies and reply form -->
                    <button onclick="toggleVisibility('replies_<?php echo $review['reviewID']; ?>')" class="bg-transparent hover:text-white hover:underline text-[#C4A484] rounded px-3 py-1 mt-2">Show/Hide Replies</button>

                    <!-- Replies section and reply form -->
                    <div id="replies_<?php echo $review['reviewID']; ?>" style="display:none; margin-top: 10px;">
                        <div><?php echo fetchReplies($conn, $review['reviewID']); ?></div>
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
            <p class="text-yellow-800 flex items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i> No positive reviews found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination mt-4 flex flex-wrap justify-center">
            <?php if ($currentPage > 1): ?>
                <button onclick="window.location.href='mostPositiveReviews.php?page=<?php echo ($currentPage - 1); ?>&productID=<?php echo $productID; ?>'" class="bg-[#9b734b] hover:bg-[#785937] text-white px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">Previous</button>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <button onclick="window.location.href='mostPositiveReviews.php?page=<?php echo $i; ?>&productID=<?php echo $productID; ?>'" class="bg-<?php echo $i == $currentPage ? '[#9b734b]' : '[#C4A484]'; ?> text-<?php echo $i == $currentPage ? 'white' : 'black'; ?> px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base"><?php echo $i; ?></button>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <button onclick="window.location.href='mostPositiveReviews.php?page=<?php echo ($currentPage + 1); ?>&productID=<?php echo $productID; ?>'" class="bg-[#9b734b] hover:bg-[#785937] text-white px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">Next</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<script>
    function toggleVisibility(id) {
        var element = document.getElementById(id);
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
</script>

</body>
</html>
