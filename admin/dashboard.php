<?php
//session_start();

include '../config/dbconnect.php';


$productCountQuery = "SELECT COUNT(*) AS total FROM product";
$reviewCountQuery = "SELECT COUNT(*) AS total FROM review";
$customerCountQuery = "SELECT COUNT(*) AS total FROM customer";
$orderCountQuery = "SELECT COUNT(*) AS total FROM orders";
$inquiriesCountQuery = "SELECT COUNT(*) AS total FROM tickets";
$callbackCountQuery = "SELECT COUNT(*) AS total FROM callback_requests";


$productCountResult = $conn->query($productCountQuery)->fetch_assoc()['total'];
$reviewCountResult = $conn->query($reviewCountQuery)->fetch_assoc()['total'];
$customerCountResult = $conn->query($customerCountQuery)->fetch_assoc()['total'];
$orderCountResult = $conn->query($orderCountQuery)->fetch_assoc()['total'];
$inquiriesCountResult = $conn->query($inquiriesCountQuery)->fetch_assoc()['total'];
$callbackCountResult = $conn->query($callbackCountQuery)->fetch_assoc()['total'];


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../resources/css/admin/dashboard.css">
  
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">
    
<header class="bg-[#543310] h-20 ">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
        <div class="flex items-center gap-[1vw]">
            <img class="w-16" src="../resources/images/Logo.png" alt="Logo">
            <h1 class="text-xl text-white font-sans"><b>WOODLAK</b></h1>
            <p class="text-xl text-white font-sans">Admin</p>
        </div>
        <div class="lg:static absolute bg-[#543310] lg:min-h-fit min-h-[39vh] left-0 top-[9%] lg:w-auto w-full flex items-center px-5 justify-center lg:justify-start text-center lg:text-right xl:contents hidden lg:flex" id="content">
            <ul class="flex lg:flex-row flex-col lg:gap-[4vw] gap-8">
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../admin">Dashboard</a>
                </li>
                <li>
                    <a class="text-white  hover:text-[#D0B8A8]" href="">Products</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../orders/view_orders_Admin/OrderList.php">Orders</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../admin/inquiry">Inquiries</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../payment_process/admin_banktrans_check/admin_panel.php">Bank Transfers</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../UserProfile/RegisteredUsers.php">Users</a>
                </li>
            </ul>
        </div>
       
     
        <div class="flex items-center gap-3">
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]">Logout</button> 
            <button onclick="responsive()"><i class="bi bi-list text-4xl lg:hidden text-white"></i></button>
        </div>
       
    </nav>
</header>
    

    <main class="min-h-screen flex flex-col items-center bg-transparent pt-6 sm:pt-0">
        <div class="w-full mx-3 sm:max-w-6xl mt-6 px-6 py-4 bg-translucent shadow-lg rounded-lg">
            <h1 class="text-center text-4xl font-extrabold mb-6 text-gray-800">Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                
                
                <div class="bg-gradient-to-r from-green-600 to-green-400 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-semibold mb-3">Products</h3>
                    <p class="text-lg">Total Products: <span class="font-bold"><?php echo $productCountResult; ?></span></p>
                </div>

                
                <div class="bg-gradient-to-r from-green-600 to-green-400 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-semibold mb-3">Reviews</h3>
                    <p class="text-lg">Total Reviews: <span class="font-bold"><?php echo $reviewCountResult; ?></span></p>
                </div>

               
                <div class="bg-gradient-to-r from-green-600 to-green-400 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-semibold mb-3">Customers</h3>
                    <p class="text-lg">Total Customers: <span class="font-bold"><?php echo $customerCountResult; ?></span></p>
                </div>

                
                <div class="bg-gradient-to-r from-green-600 to-green-400 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-semibold mb-3">Orders</h3>
                    <p class="text-lg">Total Orders: <span class="font-bold"><?php echo $orderCountResult; ?></span></p>
                </div>

              
                <div class="bg-gradient-to-r from-green-600 to-green-400 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-semibold mb-3">Inquiries</h3>
                    <p class="text-lg">Total Inquiries: <span class="font-bold"><?php echo $inquiriesCountResult; ?></span></p>
                </div>

               
                <div class="bg-gradient-to-r from-green-600 to-green-400 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-semibold mb-3">Call Back Requests</h3>
                    <p class="text-lg">Total Requests: <span class="font-bold"><?php echo $callbackCountResult; ?></span></p>
                </div>
            </div>
        </div>
    </main>
    <script src="../resources/JS/navbar.js"></script>
</body>
</html>
