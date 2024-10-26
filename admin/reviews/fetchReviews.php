<?php
include '../../config/dbconnect.php';


$productID = isset($_POST['productID']) ? intval($_POST['productID']) : 0;
$stars = isset($_POST['stars']) ? intval($_POST['stars']) : 0;
$sortInquiry = isset($_POST['sortInquiry']) ? $_POST['sortInquiry'] : 'date_desc';
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


$query = "SELECT * FROM review WHERE 1"; 


if (!empty($productID)) {
    $query .= " AND productID = ?";
}


if ($stars > 0) {
    $query .= " AND rating = ?";
}


$query .= " ORDER BY $sortBy LIMIT ? OFFSET ?";


if (!empty($productID) && $stars > 0) {
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiii', $productID, $stars, $reviewsPerPage, $offset);
} elseif (!empty($productID)) {
   
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $productID, $reviewsPerPage, $offset);
} elseif ($stars > 0) {
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $stars, $reviewsPerPage, $offset);
} else {

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $reviewsPerPage, $offset);
}


$stmt->execute();
$reviews = $stmt->get_result();



$totalQuery = "SELECT COUNT(*) as totalReviews FROM review WHERE 1";
if (!empty($productID)) {
    $totalQuery .= " AND productID = $productID";
}
if ($stars > 0) {
    $totalQuery .= " AND rating = $stars";
}
$totalResult = $conn->query($totalQuery);
$totalReviews = $totalResult->fetch_assoc()['totalReviews'];
$totalPages = ceil($totalReviews / $reviewsPerPage); 





if ($reviews && $reviews->num_rows > 0) {
    while ($review = $reviews->fetch_assoc()): ?>
        <div class="review bg-[#111827] p-4 rounded-lg mb-4">
            <h4 class="text-[#C4A484] text-lg"><b><?php echo htmlspecialchars($review['customerName']); ?></b></h4>
            <div class="flex items-center">
                <p class="ms-2 text-sm font-bold text-gray-900 dark:text-white"> &#11088; <?php echo $review['rating']; ?></p>
            </div>
            <p class="text-white"><?php echo htmlspecialchars(html_entity_decode($review['reviewText'])); ?></p>
            <p><small class="text-gray-400"><?php echo $review['createdAt']; ?></small></p>

            
            <button onclick="toggleVisibility('replies_<?php echo $review['reviewID']; ?>')" class="bg-transparent hover:text-white hover:underline text-[#C4A484] rounded px-3 py-1 mt-2">Show/Hide Replies</button>

          
            <div id="replies_<?php echo $review['reviewID']; ?>" style="display:none; margin-top: 10px;">
                <?php
                $reviewID = $review['reviewID'];
                $repliesQuery = "SELECT * FROM reviewreply WHERE reviewID = $reviewID ORDER BY createdAt ASC";
                $replies = $conn->query($repliesQuery);

                if ($replies && $replies->num_rows > 0) {
                    while ($reply = $replies->fetch_assoc()) {
                        echo "<div class='reply bg-[#1f2937] p-3 rounded-lg mt-2'>
                                <p class='text-white'><b>". (isset($reply['userName']) ? htmlspecialchars($reply['userName']) : "Anonymous User") .":</b> " . htmlspecialchars($reply['replyText']) . "</p>
                              </div>";
                    }
                } else {
                    echo "<p class='text-sm text-gray-400'>No replies yet.</p>";
                }
                ?>
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
    <?php endwhile;
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
 

} else {
    echo "<p class='text-yellow-800 flex items-center gap-2'><i class='bi bi-exclamation-circle-fill'></i> No reviews found.</p>";
}
?>
