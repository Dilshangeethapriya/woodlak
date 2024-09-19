<?php
include '../config/dbconnect.php'; // Adjust the path to your DB config

// Get product ID (this could be passed via GET or POST)
$productID = intval($_GET['PRODUCTC']);

// Initialize rating counts
$ratingCounts = [
    '5' => 0,
    '4' => 0,
    '3' => 0,
    '2' => 0,
    '1' => 0
];

$query = "SELECT rating, COUNT(*) as count FROM review WHERE productID = ? GROUP BY rating";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $productID);
$stmt->execute();
$result = $stmt->get_result();

$totalRatings = 0;
while ($row = $result->fetch_assoc()) {
    $ratingCounts[$row['rating']] = $row['count'];
    $totalRatings += $row['count'];
}

// Initialize average rating and percentages
$averageRating = 0;
$ratingPercentages = [
    '5' => 0,
    '4' => 0,
    '3' => 0,
    '2' => 0,
    '1' => 0
];

// Calculate the average rating and percentages only if there are ratings
if ($totalRatings > 0) {
    $averageRating = (5 * $ratingCounts['5'] + 4 * $ratingCounts['4'] + 3 * $ratingCounts['3'] + 2 * $ratingCounts['2'] + 1 * $ratingCounts['1']) / $totalRatings;

    // Calculate percentage for each rating
    foreach ($ratingCounts as $stars => $count) {
        $ratingPercentages[$stars] = ($count / $totalRatings) * 100;
    }
}
?>

<?php if ($totalRatings > 0): ?>
    <h2 class="text-[#C4A484] text-2xl my-5">Ratings Summary</h2>
    <div class="ratings-summary my-10 bg-[#1f2937] p-10 rounded-lg mb-4">
        <div class="average-rating mb-10">
            <span class="text-xl mb-5 text-[#C4A484]"> &#11088; <?php echo round($averageRating, 1); ?> out of 5</span>
            <p class="text-white"><?php echo $totalRatings; ?> total ratings</p>
        </div>

        <?php foreach ($ratingCounts as $stars => $count): ?>
            <div class="rating-row text-white">
                <div class="rating-text"><?php echo $stars; ?> star</div>
                <div class="rating-bar">
                    <div class="rating-fill" style="width: <?php echo round($ratingPercentages[$stars], 1); ?>%;"></div>
                </div>
                <div class="rating-percentage"><?php echo round($ratingPercentages[$stars], 1); ?>%</div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
