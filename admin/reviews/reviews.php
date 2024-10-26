<?php
include '../../config/dbconnect.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}


$productsQuery = "SELECT productID, productName FROM product ORDER BY productName ASC";
$productsResult = $conn->query($productsQuery);


$mostPositiveQuery = $conn->prepare("
    SELECT rs.*, r.reviewText, r.customerName, r.rating 
    FROM review_sentiment rs
    JOIN review r ON rs.reviewID = r.reviewID
    WHERE rs.positive_score = (
        SELECT MAX(positive_score) FROM review_sentiment
    )
    LIMIT 1
");
$mostPositiveQuery->execute();
$mostPositiveReview = $mostPositiveQuery->get_result()->fetch_assoc();


$mostNegativeQuery = $conn->prepare("
    SELECT rs.*, r.reviewText, r.customerName, r.rating 
    FROM review_sentiment rs
    JOIN review r ON rs.reviewID = r.reviewID
    WHERE rs.negative_score = (
        SELECT MAX(negative_score) FROM review_sentiment
    )
    LIMIT 1
");
$mostNegativeQuery->execute();
$mostNegativeReview = $mostNegativeQuery->get_result()->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $reviewID = intval($_POST['reviewID']);
    $replyText = htmlspecialchars($_POST['replyText']); 
    $userName = "Admin";
    
    $stmt = $conn->prepare("INSERT INTO reviewreply (reviewID,userName, replyText) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $reviewID,$userName, $replyText);

    if ($stmt->execute()) {
        $productID = intval($_REQUEST["PRODUCTC"]);
        header("Location: reviews.php");
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
    <title>Admin-Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../resources/css/admin/reviews.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Add jQuery for Ajax -->
</head>
<body>
<?php include '../../includes/adminNavbar.php' ?>

<div class="review-section mt-10 mb-36 max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(255, 255, 255, 0.8);">
    <h2 class="text-[#78350f] text-4xl mb-6 text-center"> Product Reviews</h2>

    
    <a class="text-lg text-[#78350f] font-bold hover:underline bg-transparent border border-[#9b734b] rounded-md px-2 py-1 mt-6" href="reviewsReport.php" title="View detailed product review analytics reports">
        <i class="fa fa-chart-bar mr-2"></i> Product Review Analytics Reports
    </a>

       
   
    <form id="filterForm" class="mb-6 mt-20 p-6 rounded-lg shadow-md w-full mx-auto bg-[#111827]">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
       
        <div class="flex flex-col">
            <label for="productID" class="block text-lg font-semibold text-[#C4A484] mb-3">Select Product:</label>
            <select name="productID" id="productID" class="p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 bg-white text-gray-800">
                <option value="">All</option>
                <?php if ($productsResult->num_rows > 0): ?>
                    <?php while ($product = $productsResult->fetch_assoc()): ?>
                        <option value="<?php echo $product['productID']; ?>"><?php echo htmlspecialchars($product['productName']); ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>

       
        <div class="flex flex-col">
            <label for="stars" class="block text-lg font-semibold text-[#C4A484] mb-3">Filter by Stars:</label>
            <select name="stars" id="stars" class="p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 bg-white text-gray-800">
                <option value="">All</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>
        </div>

      
        <div class="flex flex-col lg:col-span-1 sm:col-span-2">
            <label for="sortInquiry" class="block text-lg font-semibold text-[#C4A484] mb-3">Sort by:</label>
            <select name="sortInquiry" id="sortInquiry" class="p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 bg-white text-gray-800">
                <option value="date_desc">Most Recent</option>
                <option value="rating_desc">Top Reviews</option>
            </select>
        </div>
    </div>
</form>

     <div id="ratingSummary"></div>

      
    <div id="mostPositiveReviewSection" >
      
    </div>

  
    <div id="mostNegativeReviewSection" >
       
    </div>

    <div id="reviewsSection"></div>
</div>

<script>
    

$(document).ready(function() {

    $(document).ready(function() {

        
        window.fetchReviews = function(page = 1) {
            var productID = $('#productID').val();
            var stars = $('#stars').val();
            var sortInquiry = $('#sortInquiry').val(); 
        
            $.ajax({
                url: 'fetchReviews.php',
                method: 'POST',
                data: { 
                    productID: productID, 
                    stars: stars, 
                    sortInquiry: sortInquiry,
                    page: page 
                },
                success: function(response) {
                    console.log('Received response:', response);
                    $('#reviewsSection').html(response); 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error in Ajax request:', textStatus, errorThrown);
                }
            });
        }
        
        
        $('#productID, #stars, #sortInquiry').change(function() {
            fetchReviews(1); 
        });
        
        
        fetchReviews();
        });


        
         function fetchRatingSummary(productID) {
                        $.ajax({
                            url: 'ratingSummery.php',
                            method: 'POST',
                            data: { productID: productID },
                            success: function(response) {
                                $('#ratingSummary').html(response); 
                            },
                            error: function() {
                                $('#ratingSummary').html('<p>Error fetching rating summary.</p>');
                            }
                        });
                    }
        
                    
                    $('#productID').change(function() {
                        var productID = $(this).val(); 
                        fetchRatingSummary(productID);  
                    });
        
                    
                    fetchRatingSummary($('#productID').val());
          
           
        });

         
    function fetchTopReviews(productID = '') {
        $.ajax({
            url: 'fetchTopReviews.php',
            method: 'POST',
            data: { productID: productID },
            success: function(response) {
                const { mostPositive, mostNegative } = JSON.parse(response);
                $('#mostPositiveReviewSection').html(mostPositive);
                $('#mostNegativeReviewSection').html(mostNegative);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching top reviews:', textStatus, errorThrown);
            }
        });
    }

    
    $('#productID').change(function() {
        const productID = $(this).val();
        fetchTopReviews(productID); 
        fetchReviews(1); 
    });

    
    fetchTopReviews();

        function toggleVisibility(id) {
                var element = document.getElementById(id);
                if (element.style.display === 'none') {
                    element.style.display = 'block';
                } else {
                    element.style.display = 'none';
                }
            }
</script>
</body>
</html>
