<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

include "../../config/dbconnect.php";
$base_url = "http://localhost/woodlak"; 


$totalInquiries = $conn->query("SELECT COUNT(*) as totalInquiries FROM tickets")->fetch_assoc()['totalInquiries'];
$totalCallbacks = $conn->query("SELECT COUNT(*) as totalCallbacks FROM callback_requests")->fetch_assoc()['totalCallbacks'];

// inquiry chart
$inquiriesTimelineResult = $conn->query("SELECT DATE(created_at) AS inquiryDate, COUNT(*) AS inquiryCount FROM tickets WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) GROUP BY inquiryDate");
$inquiriesTimeline = $inquiriesTimelineResult->fetch_all(MYSQLI_ASSOC);

// callbacks chart
$callbacksTimelineResult = $conn->query("SELECT DATE(created_at) AS callbackDate, COUNT(*) AS callbackCount FROM callback_requests WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) GROUP BY callbackDate");
$callbacksTimeline = $callbacksTimelineResult->fetch_all(MYSQLI_ASSOC);


$inquiryDates = [];
$inquiryCounts = [];
foreach ($inquiriesTimeline as $inquiry) {
    $inquiryDates[] = $inquiry['inquiryDate'];
    $inquiryCounts[] = $inquiry['inquiryCount'];
}


$callbackDates = [];
$callbackCounts = [];
foreach ($callbacksTimeline as $callback) {
    $callbackDates[] = $callback['callbackDate'];
    $callbackCounts[] = $callback['callbackCount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Inquiry Analytics Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= $base_url ?>/resources/css/admin/inquiryReport.css">
</head>
<body>

<header class="bg-[#543310] h-20 z-50">
    <div class="flex justify-between items-center w-[95%] mx-auto h-full">
        <a href="inquiries.php" 
           class="flex items-center px-4 py-2 border border-transparent rounded-md text-white hover:scale-105 focus:outline-none transition-transform duration-200">
           <img src="<?= $base_url ?>/resources/images/inquiry/arrow.png" alt="Back" class="w-6 h-6 mr-2">
        </a>
        <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8] hover:bg-[#543310] transition-colors duration-200" 
                onclick="generatePDF('Inquiry-Analytics-Report')">Download Report</button>
    </div>
</header>

<div class="container max-w-5xl" id="container">
    <h2 class="page-title text-2xl font-bold">Customer Inquiry Analytics Report</h2>

    <div class="summary-section px-10">
        <h3 class="text-lg font-bold mb-6">Summary</h3>
        <p class="pb-6"><strong>Total Inquiries:</strong> <?php echo $totalInquiries; ?></p>
        <p><strong>Total Callbacks Requested:</strong> <?php echo $totalCallbacks; ?></p>
    </div>

    
    <div class="chart-container mb-6 px-10">
        <h3 class="text-lg font-bold">Inquiries Received Over Time</h3>
        <canvas id="inquiriesTimelineChart"></canvas>
    </div>

   
    <div class="chart-container px-10">
        <h3 class="text-lg font-bold ">Callbacks Received Over Time</h3>
        <canvas id="callbacksTimelineChart"></canvas>
    </div>

 
    <div class="inquiries-by-status px-10">
        <h3 class="text-lg font-bold">Inquiries by Status</h3>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Total Inquiries</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $inquiriesByStatusResult = $conn->query("SELECT ticketStatus, COUNT(*) AS totalByStatus FROM tickets GROUP BY ticketStatus");
                while ($status = $inquiriesByStatusResult->fetch_assoc()) {
                    echo "<tr><td>{$status['ticketStatus']}</td><td>{$status['totalByStatus']}</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <div class="callbacks-by-status px-10">
        <h3 class="text-lg font-bold">Callbacks by Status</h3>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Total Callbacks</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $callbacksByStatusResult = $conn->query("SELECT status, COUNT(*) AS totalByStatus FROM callback_requests GROUP BY status");
                while ($status = $callbacksByStatusResult->fetch_assoc()) {
                    echo "<tr><td>{$status['status']}</td><td>{$status['totalByStatus']}</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
  
    const inquiryDates = <?php echo json_encode($inquiryDates); ?>;
    const inquiryCounts = <?php echo json_encode($inquiryCounts); ?>;

  
    const callbackDates = <?php echo json_encode($callbackDates); ?>;
    const callbackCounts = <?php echo json_encode($callbackCounts); ?>;

    const inquiriesCtx = document.getElementById('inquiriesTimelineChart').getContext('2d');
new Chart(inquiriesCtx, {
    type: 'bar',
    data: {
        labels: inquiryDates,
        datasets: [{
            label: 'Inquiries',
            data: inquiryCounts,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        
        scales: {
            x: {
                type: 'time',
                time: {
                    parser: 'YYYY-MM-DD HH:mm:ss', 
                    unit: 'day', 
                    displayFormats: {
                        day: 'MMM D', 
                    }
                },
                title: {
                    display: true,
                    text: 'Date'
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1, 
                    callback: function(value) {
                        return Number.isInteger(value) ? value : null; 
                    }
                },
                title: {
                    display: true,
                    text: 'Number of Inquiries'
                }
            }
        }
    }
});


const callbacksCtx = document.getElementById('callbacksTimelineChart').getContext('2d');
new Chart(callbacksCtx, {
    type: 'bar',
    data: {
        labels: callbackDates,
        datasets: [{
            label: 'Callbacks',
            data: callbackCounts,
            borderColor: 'rgba(255, 159, 64, 1)',
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        scales: {
            x: {
                type: 'time',
                time: {
                    parser: 'YYYY-MM-DD HH:mm:ss', 
                    unit: 'day', 
                    displayFormats: {
                        day: 'MMM D', 
                    }
                },
                title: {
                    display: true,
                    text: 'Date'
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1, 
                    callback: function(value) {
                        return Number.isInteger(value) ? value : null; 
                    }
                },
                title: {
                    display: true,
                    text: 'Number of Callback Requests'
                }
            }
        }
    }
});

</script>
<script src="../../resources/JS/generatePDF.js"></script>
</body>
</html>
