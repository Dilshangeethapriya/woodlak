<?php
include '../../config/dbconnect.php';

// Fetch all products to populate the dropdown menu
$productsQuery = "SELECT productID, productName FROM product ORDER BY productName ASC";
$productsResult = $conn->query($productsQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['productID'])) {
    $productID = intval($_POST['productID']); // Get selected product ID
} else {
    $productID = null; // No product selected initially
}


if ($productID) {
    // Fetch the product name for display in the heading
    $productNameQuery = $conn->prepare("SELECT productName FROM product WHERE productID = ?");
    $productNameQuery->bind_param('i', $productID);
    $productNameQuery->execute();
    $productNameResult = $productNameQuery->get_result();
    $productNameRow = $productNameResult->fetch_assoc();
    $selectedProductName = $productNameRow['productName'];

    // Fetch reviews for the selected product
    $reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY createdAt DESC ");
    $reviewsQuery->bind_param('i', $productID);
    $reviewsQuery->execute();
    $reviews = $reviewsQuery->get_result();
} else {
    $selectedProductName = null; // No product selected
    $reviews = null;
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitReply'])) {
    $reviewID = intval($_POST['reviewID']);
    $replyText = htmlspecialchars($_POST['replyText']); // Sanitize reply text
    $adminReply = "[Admin] - ".$replyText;
    // Insert reply into database
    $stmt = $conn->prepare("INSERT INTO reviewreply (reviewID, replyText) VALUES (?, ?)");
    $stmt->bind_param('is', $reviewID, $adminReply);

    if ($stmt->execute()) {
        header("Location: reviews.php");
        exit();
    } else {
        echo "Error submitting reply: " . $conn->error;
    }
}

// Fetch reviews if a product is selected
if ($productID) {
    $reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY createdAt DESC ");
    $reviewsQuery->bind_param('i', $productID);
    $reviewsQuery->execute();
    $reviews = $reviewsQuery->get_result();
} else {
    $reviews = null; // No product selected
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="../../resources/css/admin/reviews.css">
</head>
<body >
<?php include '../../includes/adminNavbar.php' ?>

    <!-- HTML and Review Section -->
<div class="review-section mt-10 mb-36 max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(255, 255, 255, 0.8);">

    <h2 class="text-[#C4A484] text-4xl mb-4 text-center"> Product Reviews</h2>
  
  

    <a class="text-lg text-[#C4A484] font-bold hover:underline bg-transparent border border-gray-300 rounded-md px-2 py-1" href="reviewsReport.php" title="View detailed product review analytics reports">
    <i class="fa fa-chart-bar mr-2"></i> Product Review Analytics Reports
    </a>




    <!-- Product Select Form -->
    <form action="reviews.php" method="POST" class="mb-6 mt-20">
    <label for="productID" class="block text-lg text-gray-700">Select Product:</label>
    <select name="productID" id="productID" class="mt-2 p-2 border border-gray-300 rounded-md" onchange="this.form.submit()">
        <option value="">All</option>
        <?php if ($productsResult->num_rows > 0): ?>
            <?php while ($product = $productsResult->fetch_assoc()): ?>
                <option value="<?php echo $product['productID']; ?>" <?php if ($productID == $product['productID']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($product['productName']); ?>
                </option>
            <?php endwhile; ?>
        <?php endif; ?>
    </select>
</form>

   
    <?php if ($productID): ?>
    

        <!-- Display Customer Reviews -->
        <?php if ($reviews && $reviews->num_rows > 0): ?>
            <h2 class="text-[#C4A484] text-2xl my-5">Customer Reviews for <?php echo $selectedProductName ?></h2>
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
                                <button type="submit" name="submitReply" class="bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2 transition duration-150 ease-in-out">
                                    Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-yellow-800  flex items-center gap-2">
                       <i class="bi bi-exclamation-circle-fill"></i> No reviews available for this product.
             </p>
        <?php endif; ?>
    <?php else: ?>   
          <p class="text-gray-700 flex items-center gap-2">
              <i class="bi bi-info-circle-fill"></i> You haven't selected a product yet.
          </p>
</div>

<?php endif;?>

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

</body>
</html>

