<?php
include 'dbcon.php';

$type = '';

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    $fetchSql = "SELECT * FROM product";
    
    if($type == 'in'){
        $fetchSql = 'SELECT * FROM log_stockin';
    }
    else if($type == 'out'){
        $fetchSql = 'SELECT * FROM log_stockout';
    }
    else if($type == 'balance'){
        $fetchSql = "SELECT * FROM product";
    }
    else{       
        $fetchSql = "SELECT * FROM product";
    }

    if($fetchSql != ''){
        $result = mysqli_query($conn, $fetchSql);

        if ($result === FALSE) {
            die("Error fetching data: " . mysqli_connect_error());
        }
    }
    
    
}
else{
    $type = 'balance';
    $fetchSql = "SELECT * FROM product";
    if($fetchSql != ''){
        $result = mysqli_query($conn, $fetchSql);

        if ($result === FALSE) {
            die("Error fetching data: " . mysqli_connect_error());
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
        <title>Stock In and Stock Out Management</title>
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

        <script>
            function responsive() {
                var x = document.getElementById("content");
                x.classList.toggle("hidden");
            }
        </script>

        <?php include "../includes/adminNavbar.php" ?> 

        


        <div style="padding: 10px;">
        <h1 class="text-center" style="font-size:50px"><b>Stock In and Stock Out Management</b></h1>
        
        


        <div class="row" style="margin-top:20px;">
            <form action="stockReport.php">
                <label for="type" style="font-size: 18px;font-weight: 700;">Report : </label>
                <select name="type" id="type" style="width: 250px;padding: 10px;border: chocolate;border-radius: 5px;margin-left: 10px;">
                    <option value="balance" <?php echo (isset($type) && $type == 'balance') ? 'selected' : ''; ?>>Stock Balance</option>
                    <option value="in" <?php echo (isset($type) && $type == 'in') ? 'selected' : ''; ?>>Stock In</option>
                    <option value="out" <?php echo (isset($type) && $type == 'out') ? 'selected' : ''; ?>>Stock Out</option>
                </select>
                <input class="class='text-l bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-20 rounded'" type="submit" style="background-color:#78350f; border-radius: 5px;" value="View">
                <?php if (!empty($type)) : ?>

                <a href="export_pdf.php?type=<?php echo isset($type) ? $type : ''; ?>" class="button text-md bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[10%] rounded" style="background-color:#C40C0C; float:right; margin-right: 35px;">Export to PDF &nbsp;<i class="bi bi-filetype-pdf text-l"></i></a>
                <?php endif; ?>
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
    

</html>