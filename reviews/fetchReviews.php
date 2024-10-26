<?php
session_start();
include '../config/dbconnect.php'; 


$productID = intval($_POST['productID']);
$stars = isset($_POST['stars']) ? intval($_POST['stars']) : 0;
$sortInquiry = isset($_POST['sortReview']) ? $_POST['sortReview'] : 'date_desc';
$currentPage = isset($_POST['page']) ? intval($_POST['page']) : 1; 
$reviewsPerPage = 10; 
$offset = ($currentPage - 1) * $reviewsPerPage; 


switch ($sortInquiry) {
    case 'date_desc':
        $sortBy = 'createdAt DESC'; 
        break;
    case 'rating_desc':
        $sortBy = 'rating DESC'; 
        break;
    default:
        $sortBy = 'createdAt DESC'; 
}


$userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


if ($userID && $stars > 0) {
    $reviewsQuery = $conn->prepare("
        SELECT * FROM review 
        WHERE productID = ? 
        AND rating = ?
        ORDER BY (customerID = ?) DESC, $sortBy
        LIMIT ?, ?
    ");
    $reviewsQuery->bind_param('iiiii', $productID, $stars, $userID, $offset, $reviewsPerPage);
} elseif ($userID) {
    $reviewsQuery = $conn->prepare("
        SELECT * FROM review 
        WHERE productID = ? 
        ORDER BY (customerID = ?) DESC, $sortBy
        LIMIT ?, ?
    ");
    $reviewsQuery->bind_param('iiii', $productID, $userID, $offset, $reviewsPerPage);
} elseif ($stars > 0) {
    $reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? AND rating = ? ORDER BY $sortBy LIMIT ?, ?");
    $reviewsQuery->bind_param('iiii', $productID, $stars, $offset, $reviewsPerPage);
} else {
    $reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY $sortBy LIMIT ?, ?");
    $reviewsQuery->bind_param('iii', $productID, $offset, $reviewsPerPage);
}

$reviewsQuery->execute();
$reviews = $reviewsQuery->get_result();


if ($stars > 0) {
    $totalReviewsQuery = $conn->prepare("SELECT COUNT(*) AS total FROM review WHERE productID = ? AND rating = ?");
    $totalReviewsQuery->bind_param('ii', $productID, $stars);
} else {
    $totalReviewsQuery = $conn->prepare("SELECT COUNT(*) AS total FROM review WHERE productID = ?");
    $totalReviewsQuery->bind_param('i', $productID);
}

$totalReviewsQuery->execute();
$totalResult = $totalReviewsQuery->get_result();
$totalReviews = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalReviews / $reviewsPerPage); 



while ($review = $reviews->fetch_assoc()) {
    $reviewID = $review['reviewID'];
    $isUserReview = isset($userID) && $userID == $review['customerID']; 
    $isEditable = (strtotime($review['createdAt']) >= strtotime('-14 days')); 
    ?>
    <div class="review bg-[#1f2937] p-4 rounded-lg mb-4">
        <h4 class="text-[#C4A484] text-lg"><b><?php echo htmlspecialchars($review['customerName']); ?></b></h4>
        <div class="flex items-center">
            &#11088;
            <p class="ms-2 text-sm font-bold text-white"><?php echo $review['rating']; ?></p>
        </div>
        <p class="text-white"><?php echo htmlspecialchars(html_entity_decode($review['reviewText'])); ?></p>
        <p><small class="text-gray-400"><?php echo $review['createdAt']; ?></small></p>

       
        <?php if ($isUserReview && $isEditable): ?>
            <div class="mt-4">
                <button onclick="toggleVisibility('editReview_<?php echo $reviewID; ?>')" class="text-[#C4A484] hover:underline mr-1">Edit</button>
                <form action="deleteReview.php" method="POST" class="inline" onsubmit="return confirmDelete();">
                    <input type="hidden" name="reviewID" value="<?php echo $reviewID; ?>">
                    <input type="hidden" name="productID" value="<?php echo $productID; ?>">
                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                </form>
            </div>
            

          
            <div id="editReview_<?php echo $reviewID; ?>" style="display:none;" class="mt-3">
                <form action="editReview.php" method="POST">
                    <input type="hidden" name="reviewID" value="<?php echo $reviewID; ?>">
                    <input type="hidden" name="productID" value="<?php echo $productID; ?>"> 
                    <div class="mb-3">
                        <label for="editReviewText" class="block text-sm font-medium text-white">Edit Review:</label>
                        <textarea name="reviewText" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required><?php echo htmlspecialchars($review['reviewText']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editRating" class="block text-sm font-medium text-white">Edit Rating:</label>
                        <select name="rating" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                            <option value="5" <?php if ($review['rating'] == 5) echo 'selected'; ?>>5 - Excellent</option>
                            <option value="4" <?php if ($review['rating'] == 4) echo 'selected'; ?>>4 - Good</option>
                            <option value="3" <?php if ($review['rating'] == 3) echo 'selected'; ?>>3 - Average</option>
                            <option value="2" <?php if ($review['rating'] == 2) echo 'selected'; ?>>2 - Poor</option>
                            <option value="1" <?php if ($review['rating'] == 1) echo 'selected'; ?>>1 - Terrible</option>
                        </select>
                    </div>

                    <button type="submit" name="submit_edit" class="bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2">
                        Save Changes
                    </button>
                </form>
            </div>
        <?php endif; ?>

      
        <button onclick="toggleVisibility('replies_<?php echo $review['reviewID']; ?>')" class="bg-transparent hover:text-white hover:underline text-[#C4A484] rounded px-3 py-1 mt-2">Show/Hide Replies</button>

      
        <div id="replies_<?php echo $review['reviewID']; ?>" style="display:none; margin-top: 10px;">
            <?php
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
                        <input type="text" name="userName" class="mt-1 block w-full px-3 py-2 mb-3 border border-gray-300 rounded-md" placeholder="Name" required>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-100 mb-3">Replying as: <span class="font-bold"><?php echo $_SESSION['user_name']; ?></span></p>
                <?php endif; ?>
                <div>
                    <textarea name="replyText" rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Write a reply..." required></textarea>
                </div>
                <div class="text-left mt-2">
                    <button type="submit" name="submit_reply" class="bg-[#78350f] hover:bg-[#5a2b09] text-white font-semibold rounded-md px-4 py-2">
                        Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php
}
        if ($totalPages > 1) {
            echo '<div class="pagination mt-4 flex flex-wrap justify-center">';
        
            
            if ($currentPage > 1) {
                echo '<button onclick="fetchReviews('.($currentPage - 1).')" class="bg-[#9b734b] hover:bg-[#785937] text-white px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">Previous</button>';
            }
        
            
            if ($currentPage > 3) {
                echo '<button onclick="fetchReviews(1)" class="bg-[#C4A484] text-black px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">1</button>';
                if ($currentPage > 4) {
                    echo '<span class="text-gray-500 px-2 text-sm md:text-base">...</span>';
                }
            }
        
           
            for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
                if ($i == $currentPage) {
                    echo '<button class="bg-[#9b734b] text-white px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">'.$i.'</button>';
                } else {
                    echo '<button onclick="fetchReviews('.$i.')" class="bg-[#C4A484] text-black px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">'.$i.'</button>';
                }
            }
        
            
            if ($currentPage < $totalPages - 2) {
                if ($currentPage < $totalPages - 3) {
                    echo '<span class="text-gray-500 px-2 text-sm md:text-base">...</span>';
                }
                echo '<button onclick="fetchReviews('.$totalPages.')" class="bg-[#C4A484] text-black px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">'.$totalPages.'</button>';
            }
        
            
            if ($currentPage < $totalPages) {
                echo '<button onclick="fetchReviews('.($currentPage + 1).')" class="bg-[#9b734b] hover:bg-[#785937] text-white px-3 py-1 md:px-4 md:py-2 rounded-md m-1 text-sm md:text-base">Next</button>';
            }
        
            echo '</div>';
        }
?>
