
<?php
session_start();
include '../config/dbconnect.php'; 
include '../includes/phpInsight-master/autoload.php'; // Include sentiment analysis library

use PHPInsight\Sentiment; // Use PHPInsight for sentiment analysis

// Get the product ID
$productID = intval($_REQUEST["PRODUCTC"]);

// Fetch all reviews for this product
$reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY createdAt DESC");
$reviewsQuery->bind_param('i', $productID);
$reviewsQuery->execute();
$reviews = $reviewsQuery->get_result();

$productQuery = "SELECT * FROM product WHERE productID = " . $conn->real_escape_string($productID);
$productData = $conn->query($productQuery);

if ($productData->num_rows > 0) {
    $product = $productData->fetch_assoc();
    $productName = $product['productName'];
    $productImage = '../resources/' . $product['image'];
} else {
    echo "Product not found.";
}

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
        // Get the ID of the newly inserted review
        $reviewID = $stmt->insert_id;

        // Run sentiment analysis on the submitted review
        $sentiment = new Sentiment();
        $scores = $sentiment->score($reviewText);  // Get sentiment scores (positive, negative, neutral)
        $category = $sentiment->categorise($reviewText); // Get the sentiment category
        
        // Insert sentiment analysis result into the review_sentiment table
        $sentimentStmt = $conn->prepare("
            INSERT INTO review_sentiment (reviewID, positive_score, negative_score, neutral_score, sentiment_category, last_analyzed)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $sentimentStmt->bind_param('iddds', $reviewID, $scores['pos'], $scores['neg'], $scores['neu'], $category);
        $sentimentStmt->execute();

        // Redirect to the same product page after submission
        header("Location: allReviews.php?PRODUCTC=$productID");
        exit();
    } else {
        echo "Error submitting review: " . $conn->error;
    }
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $reviewID = intval($_POST['reviewID']);
    $replyText = htmlspecialchars($_POST['replyText']); // Sanitize reply text
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : $_POST['userName'];
    $userName = htmlspecialchars($userName);

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO reviewreply (reviewID, userName, replyText) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $reviewID, $userName, $replyText);

    if ($stmt->execute()) {
        $productID = intval($_REQUEST["PRODUCTC"]);
        header("Location: allReviews.php?PRODUCTC=$productID");
        exit();
    } else {
        echo "Error submitting reply: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../resources/css/ratingCounts.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body class="bg-gray-100" style=" background-image: url('../resources/images/bg2.png');background-repeat: no-repeat; background-attachment: fixed; background-size: cover;">

    <?php include '../includes/navbar.php' ?>

    <!-- Main Content -->
    <div class="container mx-auto my-10">
        <div class="review-section max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(255, 255, 255, 0.8);">
             <h2 class="text-[#9b734b] text-4xl mb-8 text-center">Customer Reviews</h2>
          <div class="ratings-summary my-1 0 bg-[#1f2937] p-8 rounded-lg shadow-lg">
             <div class="flex items-center space-x-6">
                 <!-- Product Image -->
                 <img src="<?php echo $productImage ?>" alt="<?php echo $productName ?>" class="w-40 h-40 object-cover rounded-lg border-2 border-[#C4A484] shadow-md">

                  <!-- Product Name -->
                 <div>
                     <h3 class="text-2xl font-bold text-[#C4A484] mb-2"><?php echo $productName ?></h3>
                     <p class="text-sm text-gray-400">See all the reviews and ratings for this product below</p>
                 </div>
              </div>

              <?php include 'ratingCounts.php' ?>
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
             <hr> 

              <div class="flex flex-col lg:flex-row items-center space-y-4 lg:space-y-0 lg:space-x-6 bg-[#1f2937] p-8 rounded-md">
                    <!-- Hidden input field for productID -->
                 <input type="hidden" id="productID" value="<?php echo $productID; ?>">
                   <!-- Rating Filter -->
                   <div class="flex flex-col items-center w-full lg:w-auto">
                       <label for="stars" class="block text-lg font-semibold text-[#C4A484] mb-2 w-full">Filter by Stars:</label>
                       <select name="stars" id="stars" class="mt-2 p-2 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-green-600">
                           <option value="">All</option>
                           <option value="5">5 Stars</option>
                           <option value="4">4 Stars</option>
                           <option value="3">3 Stars</option>
                           <option value="2">2 Stars</option>
                           <option value="1">1 Star</option>
                       </select>
                   </div>
               
                   <!-- Sorting Options -->
                   <div class="flex flex-col items-center w-full lg:w-auto">
                       <label for="sortReview" class="block text-lg font-semibold text-[#C4A484] mb-2 w-full">Sort by:</label>
                       <select name="sortReview" id="sortReview" class="mt-2 p-2 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-green-600">
                           <option value="date_desc">Most recent</option> 
                           <option value="rating_desc">Top Reviews</option> 
                       </select>
                   </div>
               </div>
               
         </div>

          <!-- Include the ratingCounts.php to display the rating summary -->
         
          
                
               <!-- Container for dynamically updating reviews -->
               <div id="reviewsContainer">
                   <!-- Reviews will be dynamically loaded here -->
               </div>
               
           
        </div>
    </div>
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

    <?php include '../includes/footer.php' ?>
    
    <script>
$(document).ready(function() {
   

    // Fetch reviews dynamically as you did before
    window.fetchReviews = function(page = 1) {
        var productID = $('#productID').val();
        var stars = $('#stars').val();
        var sortReview = $('#sortReview').val();

        $.ajax({
            url: 'fetchReviews.php',
            method: 'POST',
            data: { 
                productID: productID, 
                stars: stars, 
                sortReview: sortReview,
                page: page // Pass the page number
            },
            success: function(response) {
                $('#reviewsContainer').html(response); // Update the reviews section with the response data

                // Re-attach delete confirmation modal after dynamically loading content
                attachDeleteConfirmation();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error in Ajax request:', textStatus, errorThrown);
            }
        });
    };

    // Fetch reviews when filters change or the page loads
    $('#stars, #sortReview').change(function() {
        fetchReviews(1); // Reload reviews on filter change
    });

    fetchReviews(); // Initial load


    // delete confirmation-----------

    var deleteForm = null; // To store the form reference for delete

// Function to show the custom confirmation modal
function showDeleteModal(form) {
    deleteForm = form; // Store the form to submit later if confirmed
    document.getElementById('deleteModal').style.display = 'flex';
}

// Function to hide the custom confirmation modal
function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Attach delete confirmation modal to forms
function attachDeleteConfirmation() {
    $('form[onsubmit]').off('submit'); // Remove any previous handler
    $('form[onsubmit]').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        showDeleteModal(this); // Show modal and store the form reference
    });
}

// To show the modal
document.getElementById('deleteModal').style.display = 'flex';

// To hide the modal
document.getElementById('deleteModal').style.display = 'none';

// Confirm delete action
$('#confirmDeleteBtn').click(function() {
    if (deleteForm) {
        deleteForm.submit(); // Submit the form if confirmed
    }
    hideDeleteModal(); // Hide the modal
});

// Cancel delete action
$('#cancelDeleteBtn').click(function() {
    hideDeleteModal(); // Just hide the modal if canceled
});

// Initially attach the confirmation modal
attachDeleteConfirmation();
});
</script>



</body>
</html>
