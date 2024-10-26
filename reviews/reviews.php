<?php
include '../config/dbconnect.php'; 
include '../includes/phpInsight-master/autoload.php'; 

use PHPInsight\Sentiment; 

$productID = intval($_REQUEST["PRODUCTC"]);
$userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($userID) {
    $reviewsQuery = $conn->prepare("
        SELECT * FROM review 
        WHERE productID = ? 
        ORDER BY (customerID = ?) DESC, createdAt DESC
        LIMIT 10
    ");
    $reviewsQuery->bind_param('ii', $productID, $userID);
} else {
    $reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY createdAt DESC LIMIT 10");
    $reviewsQuery->bind_param('i', $productID);
}
$reviewsQuery->execute();
$reviews = $reviewsQuery->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $productID = intval($_REQUEST["PRODUCTC"]);
    $customerID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $customerName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $_POST['customerName'];
    $rating = $_POST['rating'];
    $reviewText = $_POST['reviewText'];

    $customerName = htmlspecialchars($customerName);
    $reviewText = htmlspecialchars($reviewText);

    $stmt = $conn->prepare("INSERT INTO review (productID, customerID, customerName, rating, reviewText) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iisis', $productID, $customerID, $customerName, $rating, $reviewText);
    
    if ($stmt->execute()) {
        $reviewID = $stmt->insert_id;

        $sentiment = new Sentiment();
        $scores = $sentiment->score($reviewText);  
        $category = $sentiment->categorise($reviewText); 
        
        $sentimentStmt = $conn->prepare("
            INSERT INTO review_sentiment (reviewID, positive_score, negative_score, neutral_score, sentiment_category, last_analyzed)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $sentimentStmt->bind_param('iddds', $reviewID, $scores['pos'], $scores['neg'], $scores['neu'], $category);
        $sentimentStmt->execute();

        header("Location: view_product.php?PRODUCTC=$productID");
        exit();
    } else {
        echo "Error submitting review: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $reviewID = intval($_POST['reviewID']);
    $replyText = htmlspecialchars($_POST['replyText']);
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $_POST['userName'];
    $userName = htmlspecialchars($userName);

    $stmt = $conn->prepare("INSERT INTO reviewreply (reviewID, userName, replyText) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $reviewID, $userName, $replyText);

    if ($stmt->execute()) {
        $productID = intval($_REQUEST["PRODUCTC"]);
        header("Location: view_product.php?PRODUCTC=$productID");
        exit();
    } else {
        echo "Error submitting reply: " . $conn->error;
    }
}
?>



<div class="review-section my-24  max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(220, 255, 220, 0.0);">
    <h2 class="text-[#78350f] text-4xl mb-8 text-center">Customer Reviews</h2>

    <?php include "ratingCounts.php" ?>

    
    <button onclick="toggleVisibility('reviewForm')" class="bg-[#78350f] hover:bg-[#5a2b09] text-white rounded px-4 py-2 mb-4">Leave a Review</button>
    
   
    <div id="reviewForm" style="display:none;" class="mx-auto bg-[#1f2937] p-6 rounded-lg shadow-lg">
        <form action="" method="POST" class="space-y-4">
            <?php if (!isset($_SESSION['user_name'])): ?>
                <div>
                    <label for="customerName" class="block text-white text-sm font-medium">Name:</label>
                    <input type="text" name="customerName" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-100">Reviewing as: <span class="font-bold"><?php echo $_SESSION['user_name']; ?></span></p>
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

    <hr class="my-12 h-0.5 border-t-0 bg-[#78350f]" />

   
    
    <h2 class="text-[#78350f] text-2xl my-5">Customer Reviews</h2>
    
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <?php
              $reviewID = $review['reviewID'];
              $isUserReview = isset($userID) && $userID == $review['customerID'];  
              $isEditable = (strtotime($review['createdAt']) >= strtotime('-14 days')); 
            ?>
            <div class="review bg-[#1f2937] p-4 rounded-lg mb-4">
                <h4 class="text-[#C4A484] text-lg"><b><?php echo htmlspecialchars($review['customerName']); ?></b></h4>
                <div class="flex items-center">
                    <p class="ms-2 text-sm font-bold text-gray-900 dark:text-white"> &#11088; <?php echo $review['rating']; ?></p>
                </div>
                <p class="text-white"><?php echo htmlspecialchars(html_entity_decode($review['reviewText'])); ?></p>
                <p><small class="text-gray-400"><?php echo $review['createdAt']; ?></small></p>

                <?php if ($isUserReview && $isEditable): ?>

            
            <div class="mt-4">
                <button onclick="toggleVisibility('editReview_<?php echo $reviewID; ?>')" class="text-[#C4A484] border border-[#C4A484] p-1 rounded-md mr-3">Edit</button>
                <form action="../reviews/deleteReview.php" method="POST" class="inline delete-form">
                  <input type="hidden" name="reviewID" value="<?php echo $reviewID; ?>">
                  <input type="hidden" name="productID" value="<?php echo $productID; ?>">
                <button type="button" class="text-red-500 border border-red-500 p-1 rounded-md delete-btn">Delete</button>
            </form>

            <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden" style="display: none;">
                <div class="bg-[#1f2937] p-8 rounded-lg shadow-lg w-96 text-center border-2 border-[#C4A484]">
                    <h3 class="text-2xl font-semibold text-[#C4A484] mb-4">Confirm Deletion</h3>
                    <p class="text-gray-300 mb-6">Are you sure you want to delete this review? This action cannot be undone.</p>
                    <div class="flex justify-center space-x-4">
                       <button id="confirmDeleteBtn" class="bg-[#9b734b] hover:bg-[#785937] text-white px-4 py-2 rounded-md shadow-md transition duration-200 ease-in-out">Yes, Delete</button>
                       <button id="cancelDeleteBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md shadow-md transition duration-200 ease-in-out">Cancel</button>
               </div>
           </div>
</div>
            </div>

            
            <div id="editReview_<?php echo $reviewID; ?>" style="display:none;" class="mt-3">
                <form action="../reviews/editReview.php" method="POST">
                    <input type="hidden" name="reviewID" value="<?php echo $reviewID; ?>">
                    <input type="hidden" name="productID" value="<?php echo $productID; ?>">
                    <div class="mb-3">
                        <label for="editReviewText" class="block text-sm font-medium text-white mb-1 font-semibold">Edit Review</label>
                        <textarea name="reviewText" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" required><?php echo htmlspecialchars($review['reviewText']); ?></textarea>
                    </div>

                    
                    <div class="mb-3">
                        <label for="editRating" class="block text-sm font-medium text-white">Edit Rating:</label>
                        <select name="rating" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                            <option value="5" <?php if ($review['rating'] == 5) echo 'selected'; ?>>5 - Excellent</option>
                            <option value="4" <?php if ($review['rating'] == 4) echo 'selected'; ?>>4 - Good</option>
                            <option value="3" <?php if ($review['rating'] == 3) echo 'selected'; ?>>3 - Average</option>
                            <option value="2" <?php if ($review['rating'] == 2) echo 'selected'; ?>>2 - Poor</option>
                            <option value="1" <?php if ($review['rating'] == 1) echo 'selected'; ?>>1 - Terrible</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="submit_edit" class="bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2 transition duration-150 ease-in-out">
                        Save Changes
                    </button>
                </form>
            </div>
        <?php endif; ?>

                
                <button onclick="toggleVisibility('replies_<?php echo $review['reviewID']; ?>')" class="bg-transparent hover:text-white hover:underline text-[#C4A484] rounded px-3 py-1 mt-2">Show/Hide Replies</button>

                
                <div id="replies_<?php echo $review['reviewID']; ?>" style="display:none; margin-top: 10px;">
                    <?php
                    $reviewID = $review['reviewID'];
                    $replies = $conn->query("SELECT * FROM reviewreply WHERE reviewID = $reviewID ORDER BY createdAt ASC");

                    if ($replies->num_rows > 0) {
                        while ($reply = $replies->fetch_assoc()) {
                            echo "<div class='reply bg-[#111827] p-3 rounded-lg mt-2'>
                                    <p class='text-white'><b>". (isset($reply['userName']) ? htmlspecialchars($reply['userName']) : "Anonymous User") .":</b> " . htmlspecialchars($reply['replyText']) . "</p>
                                  </div>";
                        }
                    } else {
                        echo "<p class='text-sm text-gray-400'>No replies yet.</p>";
                    }
                    ?>

                   
                    <form action="" method="POST" class="mt-3">
                          <input type="hidden" name="reviewID" value="<?php echo $review['reviewID']; ?>">
                          <?php if (!isset($_SESSION['user_name'])): ?>
                                  <div>
                                      <input type="text" name="userName" class="mt-1 block w-full px-3 py-2 mb-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Name" required>
                                  </div>
                              <?php else: ?>
                                  <p class="text-sm text-gray-100 mb-3">Replying as: <span class="font-bold"><?php echo $_SESSION['user_name']; ?></span></p>
                              <?php endif; ?>
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
        <a href="../reviews/allReviews.php?PRODUCTC=<?php echo $productID; ?>" class="text-[#78350f] text-lg hover:underline">See more reviews &#10093;&#10093; </a>
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


       // delete confirmation-----------

       var deleteForm = null; 

function showDeleteModal(form) {
    deleteForm = form; 
    document.getElementById('deleteModal').style.display = 'flex'; 
}


function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none'; 
}


document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        var form = this.closest('.delete-form'); 
        showDeleteModal(form); 
    });
});


document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteForm) {
        deleteForm.submit();
    }
    hideDeleteModal(); 
});


document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
    hideDeleteModal(); 
});    
</script>

<?php
$conn->close();
?>

