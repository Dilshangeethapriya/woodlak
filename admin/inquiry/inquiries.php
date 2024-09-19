<?php

include "../../config/dbconnect.php";


$inquiryQuery = "SELECT * FROM tickets";
$inquiries = $conn->query($inquiryQuery);


$callbackQuery = "SELECT * FROM callback_requests";
$callbackRequests = $conn->query($callbackQuery);


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Inquiries & Callbacks</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../../resources/css/admin/inquiries.css">
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">

<header class="bg-[#543310] h-20 ">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
        <div class="flex items-center gap-[1vw]">
            <img class="w-16" src="../../resources/images/Logo.png" alt="Logo">
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

    <h1 class="font-bold cursor-pointer text-3xl text-center my-8">Inquiry Management</h1>

    <?php
    // success msg
    if (isset($_GET['message'])) {
        echo '
            <div class="bg-green-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-4xl">
                <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                ' . htmlspecialchars($_GET['message']) . '
            </div>';
    }

    // Check if there is an error message in the query string
    if (isset($_GET['error'])) {
        echo '
            <div class="bg-red-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-4xl">
                <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                ' . htmlspecialchars($_GET['error']) . '
            </div>';
    }
    ?>


    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-transparent">
        <div class="w-full sm:max-w-4xl mt-6 px-6 py-4 bg-translucent shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-center text-2xl font-semibold mb-5 text-gray-700">Customer Inquiries</h2>

            <div class="flex flex-col space-y-3 scrollable-list">
                <div class="grid grid-cols-4 gap-4 bg-gray-200 p-3 rounded-t-lg text-gray-600">
                    <div class="text-left"><strong>Name</strong></div>
                    <div class="text-left"><strong>Subject</strong></div>
                    <div class="text-left"><strong>Date and Time</strong></div>
                    <div class="text-left"><strong>Status</strong></div>
                </div>

                <?php while ($inquiry = $inquiries->fetch_assoc()) { ?>
                <a href="viewInquiry.php?id=<?php echo $inquiry['ticketID']; ?>" class="hover:bg-gray-50">
                    <div class="grid grid-cols-4 gap-4 p-3 border-b border-gray-300 text-gray-700">
                        <div class="text-left"><?php echo htmlspecialchars($inquiry['name']); ?></div>
                        <div class="text-left"><?php echo htmlspecialchars($inquiry['subject']); ?></div>
                        <div class="text-left"><?php echo htmlspecialchars($inquiry['created_at']); ?></div>
                        <div class="text-left font-semibold
                            <?php if ($inquiry['ticketStatus'] == 'New') echo 'text-blue-600';
                                  elseif ($inquiry['ticketStatus'] == 'In Progress') echo 'text-yellow-500';
                                  elseif ($inquiry['ticketStatus'] == 'Closed') echo 'text-green-600'; ?>">
                            <?php echo htmlspecialchars($inquiry['ticketStatus']); ?>
                        </div>
                    </div>
                </a>
                <?php } ?>
            </div>
        </div>
     
      
        <div class="w-full sm:max-w-4xl my-16 px-6 py-4 bg-translucent shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-center text-2xl font-semibold mb-5 text-gray-700">Callback Requests</h2>

            <div class="flex flex-col space-y-3 scrollable-list">
                <div class="grid grid-cols-4 gap-4 bg-gray-200 p-3 rounded-t-lg text-gray-600">
                    <div class="text-left"><strong>Name</strong></div>
                    <div class="text-left"><strong>Phone</strong></div>
                    <div class="text-left"><strong>Time Range</strong></div>
                    <div class="text-left"><strong>Status</strong></div>
                </div>

                <?php while ($callback = $callbackRequests->fetch_assoc()) { ?>
                <a href="viewCallback.php?id=<?php echo $callback['id']; ?>" class="hover:bg-gray-50">
                    <div class="grid grid-cols-4 gap-4 p-3 border-b border-gray-300 text-gray-700">
                        <div class="text-left"><?php echo htmlspecialchars($callback['name']); ?></div>
                        <div class="text-left"><?php echo htmlspecialchars($callback['phone']); ?></div>
                        <div class="text-left"><?php echo htmlspecialchars($callback['time_from']) . ' - ' . htmlspecialchars($callback['time_to']); ?></div>
                        <div class="text-left font-bold
                            <?php if ($callback['status'] == 'Pending') echo 'text-blue-500';
                                  elseif ($callback['status'] == 'In Progress') echo 'text-yellow-500';
                                  elseif ($callback['status'] == 'Failed') echo 'text-red-500';
                                  elseif ($callback['status'] == 'Completed') echo 'text-green-600'; ?>">
                            <?php echo htmlspecialchars($callback['status']); ?>
                        </div>
                    </div>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <script src="../../resources/JS/navbar.js"></script>
</body>
</html>
