<?php
session_start();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Inquiries & Callbacks</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../resources/css/admin/inquiries.css">
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100">
    <?php include "../../includes/adminNavbar.php" ?> 

    <h1 class="font-bold cursor-pointer text-3xl text-center my-8">Inquiry Management</h1>

    <?php
    // success msg
    if (isset($_GET['message'])) {
        echo '
            <div class="bg-green-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-5xl">
                <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                ' . htmlspecialchars($_GET['message']) . '
            </div>';
    }

    // Check if there is an error message in the query string
    if (isset($_GET['error'])) {
        echo '
            <div class="bg-red-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-5xl">
                <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                ' . htmlspecialchars($_GET['error']) . '
            </div>';
    }
    ?>

    <!-- Inquiry Form -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-transparent">
        <div class="w-full sm:max-w-5xl my-6 px-6 py-4 bg-translucent shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-center text-2xl font-semibold mb-6 text-gray-700">Customer Inquiries</h2>

            <div class="searchBar my-5">
                <form id="inquiryForm" class="flex flex-col space-y-4 lg:space-y-0 lg:flex-row lg:space-x-4">
                    <div class="flex flex-col w-full lg:w-auto">
                        <input class="px-3 py-2 rounded-lg border w-full lg:w-72" 
                            type="text" 
                            placeholder="Enter Customer's Name" 
                            name="searchInquiry" 
                            id="searchInquiry">
                    </div>
                    
                    <button class="bg-gray-700 px-5 py-2 rounded-lg text-white hover:bg-gray-800 w-full lg:w-auto" type="button" id="searchButton">
                        Search
                    </button>

                    <div class="flex flex-col lg:flex-row gap-4 items-center w-full lg:w-auto">
                        <div class="flex flex-row items-center gap-2 w-full lg:w-auto">
                            <label for="sortInquiry" class="font-semibold whitespace-nowrap">Sort By:</label>
                            <select name="sortInquiry" id="sortInquiry" class="px-3 py-2 rounded-lg border w-full lg:w-auto">
                                <option value="date_asc">Date (Oldest to Newest)</option>
                                <option value="date_desc">Date (Newest to Oldest)</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                            </select>
                        </div>

                        <div class="flex flex-row items-center gap-6 lg:gap-2 w-full lg:w-auto">
                            <label for="statusFilter" class="font-semibold whitespace-nowrap">Filter:</label>
                            <select name="statusFilter" id="statusFilter" class="px-3 py-2 rounded-lg border w-full lg:w-auto">
                                <option value="">All</option>
                                <option value="New">New</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-4 gap-4 bg-gray-300 p-2 text-left rounded-t-lg font-semibold">
                <div>Name</div>
                <div>Subject</div>
                <div>Date Submitted</div>
                <div>Status</div>
            </div>

            <div id="inquiryResults" class="scrollable-list"></div> 
        </div>

        <div class="w-full sm:max-w-5xl my-6 px-6 py-4 bg-translucent shadow-md overflow-hidden sm:rounded-lg">
           <h2 class="text-center text-2xl font-semibold my-6 text-gray-700">Callback Requests</h2>

            <div class="searchBar my-5">
                <form id="callbackForm" class="flex flex-col space-y-4 lg:space-y-0 lg:flex-row lg:space-x-4">
                    <div class="flex flex-col w-full lg:w-auto">
                        <input class="px-3 py-2 rounded-lg border w-full lg:w-72" 
                            type="text" 
                            placeholder="Enter Customer's Name" 
                            name="searchCallback" 
                            id="searchCallback">
                    </div>
                    
                    <button class="bg-gray-700 px-5 py-2 rounded-lg text-white hover:bg-gray-800 w-full lg:w-auto" type="button" id="callbackSearchButton">
                        Search
                    </button>
            
                    <div class="flex flex-col lg:flex-row gap-4 items-center w-full lg:w-auto">
                        <div class="flex flex-row items-center gap-2 w-full lg:w-auto">
                            <label for="sortCallback" class="font-semibold whitespace-nowrap">Sort By:</label>
                            <select name="sortCallback" id="sortCallback" class="px-3 py-2 rounded-lg border w-full lg:w-auto">
                                <option value="date_asc">Date (Oldest to Newest)</option>
                                <option value="date_desc">Date (Newest to Oldest)</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-6 lg:gap-2 w-full lg:w-auto">
                    <label for="sortCallback" class="font-semibold whitespace-nowrap">Sort By:</label>  
                    <select name="callbackStatusFilter" id="callbackStatusFilter" class="px-3 py-2 rounded-lg border w-full lg:w-auto" onchange="submitCallbackForm()">
                        <option value=""> All </option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Failed">Failed</option>
                        <option value="Completed" > Completed </option>
                   </select>
                        </div>
                 </form>
             </div>
           
             <div class="grid grid-cols-4 gap-4 bg-gray-300 p-2 text-left rounded-t-lg font-semibold">
               <div>Name</div>
               <div>Phone</div>
               <div>Date Requested</div>
               <div>Status</div>
             </div>
           
             <div id="callbackResults" class="scrollable-list">
                 <!-- Callbacks will be loaded here via AJAX -->
             </div>
      </div>
             <div class="mx-auto mb-20 flex justify-center">
                   <a class="text-lg text-black font-bold hover:underline bg-transparent border border-gray-700 rounded-md px-2 py-1 " href="inquiryReport.php" title="View detailed inquiry analytics reports">
                      <i class="fa fa-chart-bar mr-2"></i> Inquiry Anlytic Report
                  </a>
             </div>
   </div>

    <script>
        $(document).ready(function() {
            // Function to handle AJAX request for inquiries
            function fetchInquiries() {
                $.ajax({
                    url: 'fetchInquiries.php',
                    type: 'POST',
                    data: $('#inquiryForm').serialize(),
                    success: function(response) {
                        $('#inquiryResults').html(response);
                    }
                });
            }

            // Function to handle AJAX request for callbacks
            function fetchCallbacks() {
                $.ajax({
                    url: 'fetchCallbacks.php',
                    type: 'POST',
                    data: $('#callbackForm').serialize(),
                    success: function(response) {
                        $('#callbackResults').html(response);
                    }
                });
            }

            // Trigger AJAX on search, sort, or filter change for inquiries
            $('#searchButton').click(function() {
                fetchInquiries();
            });

            $('#sortInquiry, #statusFilter').change(function() {
                fetchInquiries();
            });

            // Trigger AJAX on search or sort change for callbacks
            $('#callbackSearchButton').click(function() {
                fetchCallbacks();
            });

            $('#sortCallback').change(function() {
                fetchCallbacks();
            });

            $('#callbackStatusFilter').change(function() {
                fetchCallbacks();
            });

            // Initial load of inquiries and callbacks
            fetchInquiries();
            fetchCallbacks();
        });
    </script>
</body>
</html>

