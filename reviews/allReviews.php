
<?php
session_start();
include '../config/dbconnect.php'; 
include '../includes/phpInsight-master/autoload.php'; 

use PHPInsight\Sentiment; 

$productID = intval($_REQUEST["PRODUCTC"]);

$reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY createdAt DESC");
$reviewsQuery->bind_param('i', $productID);
$reviewsQuery->execute();
$reviews = $reviewsQuery->get_result();

$productQuery = "SELECT * FROM product WHERE productID = " . $conn->real_escape_string($productID);
$productData = $conn->query($productQuery);

if ($productData->num_rows > 0) {
    $product = $productData->fetch_assoc();
    $productName = $product['productName'];
    $productImage = '../' . $product['image'];
} else {
    echo "Product not found.";
}

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

        header("Location: allReviews.php?PRODUCTC=$productID");
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
<body class="bg-gray-100" style=" background-image: url('../resources/images/bg4.png');background-repeat: no-repeat; background-attachment: fixed; background-size: cover;">

    <?php include '../includes/navbar.php' ?>

    
    <div class="container mx-auto my-10">
        <div class="review-section max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(225, 255, 225, 0.8);">
             <h2 class="text-[#785b3a] text-4xl mb-8 text-center">Customer Reviews</h2>
          <div class="ratings-summary my-1 0 bg-[#1f2937] p-8 rounded-lg shadow-lg">
             <div class="flex items-center space-x-6">
                
                 <img src="<?php echo $productImage ?>" alt="<?php echo $productName ?>" class="w-40 h-40 object-cover rounded-lg border-2 border-[#C4A484] shadow-md">

                 
                 <div>
                     <h3 class="text-2xl font-bold text-[#C4A484] mb-2"><?php echo $productName ?></h3>
                     <p class="text-sm text-gray-400">See all the reviews and ratings for this product below</p>
                 </div>
              </div>

              <?php include 'ratingCounts.php' ?>
                
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
             <hr> 

              <div class="flex flex-col lg:flex-row items-center space-y-4 lg:space-y-0 lg:space-x-6 bg-[#1f2937] p-8 rounded-md">
                    
                 <input type="hidden" id="productID" value="<?php echo $productID; ?>">
                   
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
               
                  
                   <div class="flex flex-col items-center w-full lg:w-auto">
                       <label for="sortReview" class="block text-lg font-semibold text-[#C4A484] mb-2 w-full">Sort by:</label>
                       <select name="sortReview" id="sortReview" class="mt-2 p-2 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-green-600">
                           <option value="date_desc">Most recent</option> 
                           <option value="rating_desc">Top Reviews</option> 
                       </select>
                   </div>
               </div>
               
         </div>

 
               <div id="reviewsContainer">
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

          function toggleVisibility(id) {
        var element = document.getElementById(id);
        if (element.style.display === 'none') {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    }
    
$(document).ready(function() {
   

    
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
                page: page 
            },
            success: function(response) {
                $('#reviewsContainer').html(response); 

                attachDeleteConfirmation();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error in Ajax request:', textStatus, errorThrown);
            }
        });
    };

    
    $('#stars, #sortReview').change(function() {
        fetchReviews(1); 
    });

    fetchReviews(); 


    // delete confirmation-----------

    var deleteForm = null; 


function showDeleteModal(form) {
    deleteForm = form; 
    document.getElementById('deleteModal').style.display = 'flex';
}


function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}


function attachDeleteConfirmation() {
    $('form[onsubmit]').off('submit'); 
    $('form[onsubmit]').on('submit', function(e) {
        e.preventDefault();
        showDeleteModal(this); 
    });
}


document.getElementById('deleteModal').style.display = 'flex';


document.getElementById('deleteModal').style.display = 'none';


$('#confirmDeleteBtn').click(function() {
    if (deleteForm) {
        deleteForm.submit(); 
    }
    hideDeleteModal(); 
});


$('#cancelDeleteBtn').click(function() {
    hideDeleteModal(); 
});


attachDeleteConfirmation();
});



</script>



</body>
</html>
