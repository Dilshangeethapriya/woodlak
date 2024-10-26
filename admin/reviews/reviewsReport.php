<?php

include '../../config/dbconnect.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

$base_url = "http://localhost/woodlak"; 

$productID = isset($_GET['productID']) ? intval($_GET['productID']) : null;

$products = [];
$query = "SELECT productID, productName FROM product";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$totalRatings = 0;
$averageRating = 0;
$ratingsBreakdown = [
    '5' => 0,
    '4' => 0,
    '3' => 0,
    '2' => 0,
    '1' => 0
];
$topRatedProducts = [];
$mostReviewedProducts = [];

$whereClause = $productID ? "WHERE productID = ?" : "";

$query = "SELECT COUNT(*) as totalRatings FROM review $whereClause";
$stmt = $conn->prepare($query);
if ($productID) {
    $stmt->bind_param('i', $productID);
}
$stmt->execute();
$result = $stmt->get_result();
$totalRatings = $result->fetch_assoc()['totalRatings'];

$query = "SELECT AVG(rating) as averageRating FROM review $whereClause";
$stmt = $conn->prepare($query);
if ($productID) {
    $stmt->bind_param('i', $productID);
}
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();
$averageRating = ($row['averageRating'] !== null) ? round($row['averageRating'], 1) : 0;


$query = "SELECT rating, COUNT(*) as count FROM review $whereClause GROUP BY rating";
$stmt = $conn->prepare($query);
if ($productID) {
    $stmt->bind_param('i', $productID);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $ratingsBreakdown[$row['rating']] = $row['count'];
}

if (!$productID) {
    $query = "SELECT productID, AVG(rating) as averageRating, COUNT(*) as totalRatings 
              FROM review 
              GROUP BY productID 
              HAVING totalRatings > 10 
              ORDER BY averageRating DESC 
              LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $topRatedProducts[] = $row;
    }
}

if (!$productID) {
    $query = "SELECT productID, COUNT(*) as totalRatings 
              FROM review 
              GROUP BY productID 
              ORDER BY totalRatings DESC 
              LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $mostReviewedProducts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">  

  <title>Customer  
 Ratings Report</title>
 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../resources/css/admin/reviewsReport.css"> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>


</head>
<body>
<header class="bg-[#543310] h-20 w-full z-50 fixed top-0 flex items-center">
    <div class="flex justify-between items-center w-[95%] mx-auto h-full">
        <a href="reviews.php" 
           class="flex items-center px-4 py-2 border border-transparent rounded-md text-white hover:scale-105 focus:outline-none transition-transform duration-200">
           <img src="<?= $base_url ?>/resources/images/inquiry/arrow.png" alt="Back" class="w-6 h-6 mr-2">
        </a>

        <form method="GET" action="" class="flex items-center space-x-4">
            <select name="productID" id="productID" class="px-4 py-2 rounded-md bg-white text-black focus:outline-none" onchange="this.form.submit()">
                <option value="">All Products</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['productID']; ?>" <?php echo $productID == $product['productID'] ? 'selected' : ''; ?>>
                        <?php echo $product['productName']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8] hover:bg-[#543310] transition-colors duration-200" 
        onclick="generatePDF('Customer-ratings-analytics')">Download Report</button>
    </div>
</header>


<div class="mt-20">

</div>



<div class="container mt-40 max-w-5xl" id="container"> 
  <h2 class="page-ittle text-2xl font-bold">Customer Ratings Analytics Report</h2>

  <div class="total-ratings">
    <h3 class="text-lg font-bold">Total Ratings and Average Rating</h3>
    <p class="mt-4"><strong>Total Ratings:</strong> <?php echo $totalRatings; ?></p>
    <p class="mt-4"><strong>Average Rating:</strong> <?php echo $averageRating; ?> out of 5</p>
  </div>

  <div class="ratings-chart">
    <h3 class="text-lg font-bold">Ratings Breakdown</h3>
    <canvas id="ratingsPieChart"></canvas>
  </div>

  <?php if (!$productID): ?>
  <div class="top-products">
    <h3 class="text-lg font-bold">Top Rated Products</h3>
    <table>
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Average Rating</th>
          <th>Total Ratings</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topRatedProducts as $product): ?>
          <tr>
            <td><?php echo $product['productID']; ?></td>
            <td><?php echo round($product['averageRating'], 1); ?></td>
            <td><?php echo $product['totalRatings']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="most-reviewed-products">
    <h3 class="text-lg font-bold">Most Reviewed Products</h3>
    <table>
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Total Reviews</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mostReviewedProducts as $product): ?>
          <tr>
            <td><?php echo $product['productID']; ?></td>
            <td><?php echo $product['totalRatings']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
  const ctx = document.getElementById('ratingsPieChart').getContext('2d');
  const ratingsPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
      datasets: [{
        label: '# of Ratings',
        data: [
          <?php echo $ratingsBreakdown['5']; ?>,
          <?php echo $ratingsBreakdown['4']; ?>,
          <?php echo $ratingsBreakdown['3']; ?>,
          <?php echo $ratingsBreakdown['2']; ?>,
          <?php echo $ratingsBreakdown['1']; ?>
        ],
        backgroundColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)'
        ],
        borderColor: 
 [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)'
        ],
        borderWidth: 1
      }]
    },
    options:
 {
      responsive: true,
      plugins: {
        legend: {
          position: 
 'top',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
              const percentage = ((context.raw / total) * 100).toFixed(2);
              return context.label + ': ' + context.raw + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });


</script>
<script src="../../resources/JS/generatePDF.js"></script>

</body>
</html>