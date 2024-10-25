<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Tracking page</title>
    <link rel="stylesheet" type="text/css" href="order_tracking.css">
    <link rel="stylesheet" href="nav.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
<?php include '../../includes/navbar.php'; ?>

    <script>
        function responsive() {
            var x = document.getElementById("content");
            if (x.classList.contains("hidden")) {
                x.classList.remove("hidden");
            } else {
                x.classList.add("hidden");
            }
        }
    </script>

    <div class="terms">
        <main class="background">
            <h6>Order Tracking</h6>
            <h2>Please enter your Order ID in the below box</h2>

            <form method="GET" action="">
                <div class="center-box gap-3">
                    <input type="text" id="orderId" name="orderId" placeholder="Enter Order ID"><br>  <br>
                    <button type="submit" class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8] ">Track Order</button>
                </div>
            </form>
            <div class="center-box">
            <div class="order-details mt-6">
                <?php
                $conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                if (isset($_GET['orderId']) && !empty($_GET['orderId'])) {
                    $orderId = mysqli_real_escape_string($conn, $_GET['orderId']);
                    
         
         $query = "SELECT name, total, paymentMethod, orderStatus, combPurchased, quantity, houseNo, streetName, city, postalCode 
          FROM orders WHERE orderID = '$orderId'";

                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        echo "<h3>Order Details</h3>";
                        echo "<p><b>Name:</b> " . $row['name'] . "</p>";
                        $address = $row['houseNo'] . ', ' . $row['streetName'] . ', ' . $row['city'] . ', ' . $row['postalCode'];
                         echo "<p><b>Address:</b> " . $address . "</p>";
                        echo "<p><b>Total:</b> " . $row['total'] . "</p>";
                        echo "<p><b>Payment Method:</b> " . $row['paymentMethod'] . "</p>";
                        echo "<p><b>Order Status:</b> " . $row['orderStatus'] . "</p>";
                        echo "<p><b>Comb Purchased:</b> " . $row['combPurchased'] . "</p>";
                        echo "<p><b>Comb Amount:</b> " . $row['quantity'] . "</p>";
                        
                       
                    } else {
                        echo "<p>No order found with ID: $orderId</p>";
                        
       
                    }
                    
                   
                }
               
                ?>
            </div>
            </div>
        </main>
    </div>
</body>

</html>
