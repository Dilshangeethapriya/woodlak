<?php

include '../config/dbconnect.php';



$productCountQuery = "SELECT COUNT(*) AS total FROM product";
$reviewCountQuery = "SELECT COUNT(*) AS total FROM review";
$customerCountQuery = "SELECT COUNT(*) AS total FROM customer";
$orderCountQuery = "SELECT COUNT(*) AS total FROM orders";
$inquiriesCountQuery = "SELECT COUNT(*) AS total FROM tickets";
$callbackCountQuery = "SELECT COUNT(*) AS total FROM callback_requests";


$inquiriesQuery = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
    FROM tickets
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month";


$callbacksQuery = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
    FROM callback_requests
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month";

    $ordersQuery = "
    SELECT DATE_FORMAT(orderDate, '%Y-%m') AS month, COUNT(*) AS total
    FROM orders
    WHERE orderDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month";






$productCountResult = $conn->query($productCountQuery)->fetch_assoc()['total'];
$reviewCountResult = $conn->query($reviewCountQuery)->fetch_assoc()['total'];
$customerCountResult = $conn->query($customerCountQuery)->fetch_assoc()['total'];
$orderCountResult = $conn->query($orderCountQuery)->fetch_assoc()['total'];
$inquiriesCountResult = $conn->query($inquiriesCountQuery)->fetch_assoc()['total'];
$callbackCountResult = $conn->query($callbackCountQuery)->fetch_assoc()['total'];


// quiries for the charts
$inquiriesResult = $conn->query($inquiriesQuery);
$callbacksResult = $conn->query($callbacksQuery);
$ordersResult = $conn->query($ordersQuery);

$inquiriesData = [];
$callbacksData = [];
$ordersData = [];



// Loop through the results and prepare data for the chart
while ($row = $inquiriesResult->fetch_assoc()) {
    $inquiriesData[$row['month']] = $row['total'];
}


while ($row = $callbacksResult->fetch_assoc()) {
    $callbacksData[$row['month']] = $row['total'];
}

while ($row = $ordersResult->fetch_assoc()) {
    $ordersData[$row['month']] = $row['total'];
}




$months = [];
$inquiriesCounts = [];
$callbacksCounts = [];
$ordersCounts = [];

// Generate last 6 months dynamically
for ($i = 5; $i >= 0; $i--) {
    $month = date("Y-m", strtotime("first day of -$i months"));
    $months[] = $month;

    // Populate inquiries, callbacks, and orders counts with 0 if data is missing
    $inquiriesCounts[] = isset($inquiriesData[$month]) ? $inquiriesData[$month] : 0;
    $callbacksCounts[] = isset($callbacksData[$month]) ? $callbacksData[$month] : 0;
    $ordersCounts[] = isset($ordersData[$month]) ? $ordersData[$month] : 0;
}



$reviewCountQuery = "
    SELECT 
        rating, 
        COUNT(*) AS count 
    FROM review 
    GROUP BY rating";

$result = $conn->query($reviewCountQuery);

$positiveReviews = 0;
$neutralReviews = 0;
$negativeReviews = 0;

// Aggregate counts based on rating values
while ($row = $result->fetch_assoc()) {
    $rating = (int)$row['rating'];
    $count = (int)$row['count'];
    
    if ($rating >= 4) {
        $positiveReviews += $count;
    } elseif ($rating === 3) {
        $neutralReviews += $count;
    } elseif ($rating <= 2) {
        $negativeReviews += $count;
    }
}






$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../resources/css/admin/dashboard.css">
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">
<?php include "../includes/adminNavbar.php" ?>       
<main class="min-h-screen flex flex-col items-center bg-transparent pt-6 sm:pt-0">
    <div class="w-full mx-3 sm:max-w-6xl my-6 px-6 py-4 bg-translucent shadow-lg rounded-lg">
        <h1 class="text-center text-4xl font-extrabold mb-6 text-gray-800">Dashboard</h1>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-14">
           <!-- Total Products Tile -->
          
           <a href="products/product_detail.php">
           <div class="infoTile bg-gradient-to-r from-green-600 to-green-400 p-6 rounded-lg shadow-lg text-white flex items-center">
               <i class="fa-solid fa-bag-shopping text-4xl mr-4"></i>
               <div>
                   <h3 class="text-3xl font-bold mb-1"><?php echo $productCountResult; ?></h3>
                   <p class="text-lg font-semibold">Total Products</p>
               </div>
           </div>
           </a>
             
           <!-- Total Reviews Tile -->
           <a href="reviews/reviews.php">
           <div class="infoTile bg-gradient-to-r from-blue-600 to-blue-400 p-6 rounded-lg shadow-lg text-white flex items-center">
               <i class="fa-solid fa-star text-4xl mr-4"></i>
               <div>
                   <h3 class="text-3xl font-bold mb-1"><?php echo $reviewCountResult; ?></h3>
                   <p class="text-lg font-semibold">Total Reviews</p>
               </div>
           </div>
           </a>
          
           <!-- Total Customers Tile -->
           <div class="infoTile bg-gradient-to-r from-yellow-500 to-yellow-400 p-6 rounded-lg shadow-lg text-white flex items-center">
               <i class="fa-solid fa-users text-4xl mr-4"></i>
               <div>
                   <h3 class="text-3xl font-bold mb-1"><?php echo $customerCountResult; ?></h3>
                   <p class="text-lg font-semibold">Total Customers</p>
               </div>
           </div>
       
           <!-- Total Orders Tile -->
           <div class="infoTile bg-gradient-to-r from-purple-600 to-purple-400 p-6 rounded-lg shadow-lg text-white flex items-center">
               <i class="fa-solid fa-box text-4xl mr-4"></i>
               <div>
                   <h3 class="text-3xl font-bold mb-1"><?php echo $orderCountResult; ?></h3>
                   <p class="text-lg font-semibold">Total Orders</p>
               </div>
           </div>
       
           <!-- Total Inquiries Tile -->
           <div class="infoTile bg-gradient-to-r from-orange-600 to-orange-400 p-6 rounded-lg shadow-lg text-white flex items-center">
               <i class="fa-solid fa-envelope text-4xl mr-4"></i>
               <div>
                   <h3 class="text-3xl font-bold mb-1"><?php echo $inquiriesCountResult; ?></h3>
                   <p class="text-lg font-semibold">Total Inquiries</p>
               </div>
           </div>
       
           <!-- Total Callback Requests Tile -->
           <div class="infoTile bg-gradient-to-r from-red-600 to-red-400 p-6 rounded-lg shadow-lg text-white flex items-center">
               <i class="fa-solid fa-phone text-4xl mr-4"></i>
               <div>
                   <h3 class="text-3xl font-bold mb-1"><?php echo $callbackCountResult; ?></h3>
                   <p class="text-lg font-semibold">Total Callbacks</p>
               </div>
    </div>
</div>
        
                 <div class="grid grid-cols-1  gap-6 w-full mt-20">
            
        <div class="p-6 rounded-lg shadow-lg bg-white">
                <h3 class="text-2xl font-semibold mb-3">Order Statistics</h3>
                <canvas id="orderChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 w-full mt-6">
         
          
                     <div class="p-6 rounded-lg shadow-lg bg-white span-2">
                             <h3 class="text-2xl font-semibold mb-3">Customer Reviews</h3>
                             <canvas id="reviewChart"></canvas>
                     </div>
              
                         <div class="p-6 rounded-lg shadow-lg bg-white">
                             <h3 class="text-2xl font-semibold mb-3">Inquiries & Callbacks</h3>
                             <canvas id="inquiryChart"></canvas>
                          </div>
        </div>

    </div>
</main>


<script>
    
const months = <?php echo json_encode($months); ?>;
      
        const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"
];


const monthString = months.map(dateString => {
  const monthIndex = parseInt(dateString.slice(5, 7)) - 1;
  return monthNames[monthIndex];
});
        const inquiriesCounts = <?php echo json_encode($inquiriesCounts); ?>;
        const callbacksCounts = <?php echo json_encode($callbacksCounts); ?>;
        const ordersCounts = <?php echo json_encode($ordersCounts); ?>;

    

    // Review Statistics
    const reviewChart = new Chart(document.getElementById('reviewChart'), {
    type: 'bar',
    data: {
        labels: ['Reviews'], // Single x-axis label for the bar chart
        datasets: [
            {
                label: 'Positive', // Label for positive reviews
                data: [<?php echo $positiveReviews; ?>], // Data for positive reviews
                backgroundColor: '#36A2EB', // Color for positive reviews
            },
            {
                label: 'Negative', // Label for negative reviews
                data: [<?php echo $negativeReviews; ?>], // Data for negative reviews
                backgroundColor: '#FF6384', // Color for negative reviews
            },
            {
                label: 'Neutral', // Label for neutral reviews
                data: [<?php echo $neutralReviews; ?>], // Data for neutral reviews
                backgroundColor: '#FFCE56', // Color for neutral reviews
            }
        ]
    },
});

    // Order Statistics
    const orderChart = new Chart(document.getElementById('orderChart'), {
        type: 'line',
        data: {
            labels: monthString,
            datasets: [{
                label: 'Orders',
                data: ordersCounts,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 220, 192, 1)',
                borderWidth: 2
            }]
        },
    });

    // Inquiries and Callbacks
    const inquiryChart = new Chart(document.getElementById('inquiryChart'), {
        type: 'line', // You can use 'bar' for a bar chart
            data: {
                labels: monthString,
                datasets: [
                    {
                        label: 'Inquiries',
                        data: inquiriesCounts,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2
                    },
                    {
                        label: 'Callback Requests',
                        data: callbacksCounts,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Requests'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
    });
</script>

</body>
</html>
























