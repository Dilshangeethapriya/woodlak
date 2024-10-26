<?php
include '../config/dbconnect.php'; 

$productID = intval($_GET['PRODUCTC']);

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

$averageRating = 0;
$ratingPercentages = [
    '5' => 0,
    '4' => 0,
    '3' => 0,
    '2' => 0,
    '1' => 0
];

if ($totalRatings > 0) {
    $averageRating = (5 * $ratingCounts['5'] + 4 * $ratingCounts['4'] + 3 * $ratingCounts['3'] + 2 * $ratingCounts['2'] + 1 * $ratingCounts['1']) / $totalRatings;

    foreach ($ratingCounts as $stars => $count) {
        $ratingPercentages[$stars] = ($count / $totalRatings) * 100;
    }
}
?>

<?php if ($totalRatings > 0): ?>
    
    <div class="ratings-summary my-5 bg-[#1f2937] p-10 rounded-lg mb-4">
        <h2 class="text-white text-2xl font-bold text-center mx-auto mb-5">Ratings Summary</h2>
        <div class="text-center mb-6">
            <span class="text-2xl font-bold text-[#C4A484]"><?php echo round($averageRating, 1); ?> / 5</span>
            <div class="text-sm text-gray-400"><?php echo $totalRatings; ?> total ratings</div>
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
