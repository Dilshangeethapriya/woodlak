<?php
include 'dbcon.php';

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'balance';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$fetchSql = "SELECT * FROM product";

if ($type == 'in') {

    $fetchSql = "SELECT * FROM log_stockin WHERE date >= '$start_date' AND date <= '$end_date'";

} elseif ($type == 'out') {

    $fetchSql = "SELECT * FROM log_stockout WHERE date >= '$start_date' AND date <= '$end_date'";

} elseif ($type == 'balance') {
    
    $fetchSql = "SELECT * FROM product";  
}

if($fetchSql != ''){
    $result = mysqli_query($conn, $fetchSql);

    if ($result === FALSE) {
        die("Error fetching data: " . mysqli_connect_error());
    }
}   
?>
<!DOCTYPE html>
<html>
<head>
        <title>Stock Management</title>
        <meta charset="utf-8">
		<meta name="veiwport" content="width=device-width,intial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="addProduct.css">
        <style>
            body{
                background:#c19a6b;   
            }
        </style>
    </head>
    <body>
        <?php include "../../includes/adminNavbar.php" ?>  

        <div style="padding: 10px;">
        <h1 class="text-center" style="font-size:50px"><b>Stock Management</b></h1>
        
        <div class="row" style="margin-top:20px;">
            <form class="w-full" action="stockReport.php" method="GET">
            <div class="flex flex-wrap -mx-3 mb-2 ml-2">
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <label for="type" style="font-size: 18px;font-weight: 700;">Report : </label>
                    <select name="type" id="type" class="block w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" onchange="toggleDateRange()">
                        <option value="balance" <?php echo (isset($type) && $type == 'balance') ? 'selected' : ''; ?>>Stock Balance</option>
                        <option value="in" <?php echo (isset($type) && $type == 'in') ? 'selected' : ''; ?>>Stock In</option>
                        <option value="out" <?php echo (isset($type) && $type == 'out') ? 'selected' : ''; ?>>Stock Out</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4 px-3" id="start-date-range" style="display: none;">
                    <label for="start_date" style="font-size: 18px;font-weight: 700;">Start Date:</label>
                    <input type="date" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="start_date"  id="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                </div>
                <div class="w-full md:w-1/4 px-3" id="end-date-range" style="display: none;">
                    <label for="end_date" style="font-size: 18px;font-weight: 700;margin-left: 10px;">End Date:</label>
                    <input type="date" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                </div>
                <div class="w-full md:w-1/12 px-3">
                <button type="submit" class="text-white font-medium rounded-lg text-md w-full sm:w-auto px-5 py-2.5 text-center" style="margin-top: 25px;background-color:#78350f;height: 55px;">View</button>
                </div>

                    <?php if (!empty($type)) : ?>

                    <a href="export_pdf.php?type=<?php echo isset($type) ? $type : ''; ?>&start_date=<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>&end_date=<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>" class="text-white font-medium rounded-lg text-md w-full sm:w-auto sm:ml-5 px-4 py-2.5 text-center ml-0 sm:ml-[40px]" style="background-color:#C40C0C; height: 55px;margin-top: 25px; float: right;padding-top: 15px;">Export to PDF &nbsp;<i class="bi bi-filetype-pdf text-l"></i></a>
                    <?php endif; ?>
                </div>
                </div>
            </form>
        </div>
        </div>

        <div class="table-container mt-4" style="padding: 20px;">
        <table class="table table-responsive table-hover table-striped" style="border-collapse: separate; border-spacing: 0; border: 2px solid #eaddca; border-radius: 12px; overflow: hidden;">
            <thead class="table-light" >
                <?php
                    if (isset($_GET['type']) && $_GET['type'] == 'balance'){
                        echo '<h3 style="font-size: xx-large;font-weight: 500;margin-bottom: 10px;">Stock Balance Report</h3>';
                        echo '<tr>';
                                echo '<th scope="col" style="background-color: #eaddca;">Product ID</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Name</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Balance</th>';
                                echo '</tr>';

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['productID'] . '</td>';
                                echo '<td>' . $row['productName'] . '</td>';
                                echo '<td>' . $row['stockLevel'] . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="2">No products found.</td></tr>';
                        }
                        
                    }
                    if(!isset($_GET['type'])){
                        echo '<h3 style="font-size: xx-large;font-weight: 500;margin-bottom: 10px;">Stock Balance Report</h3>';
                        echo '<tr>';
                                echo '<th scope="col" style="background-color: #eaddca;">Product ID</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Name</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Stock Balance</th>';
                                echo '</tr>';

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['productID'] . '</td>';
                                echo '<td>' . $row['productName'] . '</td>';
                                echo '<td>' . $row['stockLevel'] . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="2">No products found.</td></tr>';
                        }
                    }
                    if (isset($_GET['type']) && $_GET['type'] == 'in'){
                        echo '<h4 style="font-size: xx-large;font-weight: 500;margin-bottom: 10px;">Stock In Report</h4>';
                        echo '<tr>';
                                echo '<th scope="col" style="background-color: #eaddca;">Date</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Product ID</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Name</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Quantity</th>';
                                echo '</tr>';

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['date'] . '</td>';
                                echo '<td>' . $row['productId'] . '</td>';
                                echo '<td>' . $row['productName'] . '</td>';
                                echo '<td>' . $row['qty'] . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4">No data found.</td></tr>';
                        }
                        
                    }
                    if (isset($_GET['type']) && $_GET['type'] == 'out'){
                        echo '<h4 style="font-size: xx-large;font-weight: 500;margin-bottom: 10px;">Stock Out Report</h4>';
                        echo '<tr>';
                                echo '<th scope="col" style="background-color: #eaddca;">Date</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Product ID</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Name</th>';
                                echo '<th scope="col" style="background-color: #eaddca;">Quantity</th>';
                                echo '</tr>';

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['date'] . '</td>';
                                echo '<td>' . $row['productId'] . '</td>';
                                echo '<td>' . $row['productName'] . '</td>';
                                echo '<td>' . $row['qty'] . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4">No data found.</td></tr>';
                        }
                        
                    }
                ?>
                
            </thead>
            <tbody>
            
            </tbody>
            </table>
        </div>
       
        
    </body>
    <script>
        function toggleDateRange() {
            var type = document.getElementById("type").value;
            var startDateRange = document.getElementById("start-date-range");
            var endDateRange = document.getElementById("end-date-range");

            if (type === 'in' || type === 'out') {
                startDateRange.style.display = 'inline-block';                
                endDateRange.style.display = 'inline-block';
            } else {
                startDateRange.style.display = 'none';
                endDateRange.style.display = 'none';
            }

            setDefaultDates();
        }

        function setDefaultDates() {
            var today = new Date();
            var endDate = today.toISOString().split('T')[0];

            var lastMonth = new Date();
            lastMonth.setDate(today.getDate() - 30);
            var startDate = lastMonth.toISOString().split('T')[0];

            document.getElementById("start_date").value = startDate;
            document.getElementById("end_date").value = endDate;
        }

        window.onload = function() {
            toggleDateRange();
        };
    </script>
</html>