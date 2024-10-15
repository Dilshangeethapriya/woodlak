<?php
include '../../config/dbconnect.php'; // Adjust the path if needed

// Function to calculate percentage
function calculatePercentage($count, $total) {
    return ($total > 0) ? round(($count / $total) * 100) : 0;
}

// Check if a product ID is provided or if we should show ratings for all products
if (isset($_POST['productID']) && !empty($_POST['productID'])) {
    $productID = intval($_POST['productID']);
    
    // Query to fetch the summary of reviews for the selected product
    $query = "SELECT rating, COUNT(*) AS count FROM review WHERE productID = $productID GROUP BY rating";
    $result = $conn->query($query);
    
    $productQuery = "SELECT productName FROM product WHERE productID = " . $conn->real_escape_string($productID);
    $productData = $conn->query($productQuery);

        if ($productData->num_rows > 0) {
            $product = $productData->fetch_assoc();
            $productName = $product['productName'];
        } else {
            // Handle case where product not found
            echo "Product not found.";
        }

    // Initialize variables for total ratings and star count
    $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    $totalRatings = 0;
    
    // Fetch results and count ratings
    while ($row = $result->fetch_assoc()) {
        $ratingCounts[$row['rating']] = $row['count'];
        $totalRatings += $row['count'];
    }
    
    // Calculate the average rating
    $averageRating = 0;
    if ($totalRatings > 0) {
        $averageRating = (1 * $ratingCounts[1] + 2 * $ratingCounts[2] + 3 * $ratingCounts[3] + 4 * $ratingCounts[4] + 5 * $ratingCounts[5]) / $totalRatings;
    }

} else {
    // If no product is selected, fetch the rating summary for all products
    $query = "SELECT rating, COUNT(*) AS count FROM review GROUP BY rating";
    $result = $conn->query($query);

    // Initialize variables for total ratings and star count
    $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    $totalRatings = 0;
    
    // Fetch results and count ratings
    while ($row = $result->fetch_assoc()) {
        $ratingCounts[$row['rating']] = $row['count'];
        $totalRatings += $row['count'];
    }
    
    // Calculate the average rating for all products
    $averageRating = 0;
    if ($totalRatings > 0) {
        $averageRating = (1 * $ratingCounts[1] + 2 * $ratingCounts[2] + 3 * $ratingCounts[3] + 4 * $ratingCounts[4] + 5 * $ratingCounts[5]) / $totalRatings;
    }
}
?>

<div class="rating-summary bg-[#111827] p-5 my-5 mx-auto w-full rounded-lg shadow-md">
    <h3 class="text-2xl font-bold mb-4 text-center text-white"><?php echo isset($productID) ? "Ratings Summary for $productName" : "Overall Ratings Summary for All Products"; ?></h3>
    <div class="text-center mb-6">
        <span class="text-3xl font-bold text-[#C4A484]"><?php echo round($averageRating, 1); ?> / 5</span>
        <div class="text-sm text-gray-400"><?php echo $totalRatings; ?> total ratings</div>
    </div>
    
    <div class="space-y-2">
        <?php for ($star = 5; $star >= 1; $star--): ?>
            <div class="flex items-center">
                <span class="text-sm font-bold text-white"><?php echo $star; ?> star</span>
                <div class="relative flex-grow h-4 bg-[#EDEDED] rounded-full mx-3">
                    <div class="absolute top-0 left-0 h-full bg-[#C4A484] rounded-full" style="width: <?php echo calculatePercentage($ratingCounts[$star], $totalRatings); ?>%;"></div>
                </div>
                <span class="text-sm font-semibold text-white"> <?php echo $ratingCounts[$star]; ?> (<?php echo calculatePercentage($ratingCounts[$star], $totalRatings); ?>%)</span>
            </div>
        <?php endfor; ?>
    </div>
</div>


