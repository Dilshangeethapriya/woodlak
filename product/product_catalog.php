<?php
include 'dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Catalog</title>
    <meta charset="utf-8">
	<meta name="veiwport" content="width=device-width,intial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="productCatalog.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
    <body>
    <?php include  '../includes/navbar.php'; ?>
<div class="min-h-screen flex flex-col justify-between">

    <!-- Your main content goes here -->
    <div class="flex-grow">
    <div class="catalog grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 p-8 mt-10">
            <?php
            $sql = "SELECT productID, productName, price, image FROM product";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product bg-white p-6 rounded-lg shadow-lg " style="height: 700px">';
                    echo '<a href="view_product.php?PRODUCTC='.$row['productID'].'"><img src="' . $row['image'] . '" alt="' . $row['productName'] . '" class="w-full h-[55%] object-cover mb-4 rounded-lg"></a>';
                    echo '<a href="view_product.php?PRODUCTC='.$row['productID'].'"><h2 class="text-4xl font-bold mb-2 2xl:mb-9 text-center hover:text-[#5a2b09] mb-6">' . $row['productName'] . '</h2></a>';
                    echo '<p class="price text-3xl my-16 text-center">Rs.' . $row['price'] . '</p>';
                    echo '<form method="POST" action="add_to_cart.php">';
                    echo '<input type="hidden" name="productID" value="' . $row['productID'] . '">';
                    echo '<input type="hidden" name="productName" value="' . $row['productName'] . '">';
                    echo '<input type="hidden" name="price" value="' . $row['price'] . '">';
                    echo '<button type="submit" class="bg-[#78350f] hover:bg-[#5a2b09] text-white rounded-full px-10 py-3 text-xl border-2 border-[#78350f] mx-auto mb-4 flex items-center mt-20">';
                    echo '<b>Add to Cart</b>';
                    echo '</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }

            mysqli_close($conn);
            ?>
        </div>
        <a href="shopping_cart.php" class="shoppingCart bg-[#78350f] hover:bg-white text-white hover:text-[#78350f] fixed bottom-3 right-5  p-3 rounded-full shadow-lg">
        <i class="bi bi-cart4 text-4xl"></i>
        </a>
    </div>

    <!-- Footer -->
    <footer class="bg-[#543310] text-white mt-10">
    <div class="container mx-auto py-6 px-4 flex flex-col md:flex-row justify-between items-center">
        <!-- Social Media Links -->
        <div class="flex justify-center md:justify-start mb-4 md:mb-0">
            <a href="https://web.facebook.com/woodlak123" class="text-white mx-2 hover:text-gray-400">
                <i class="bi bi-facebook text-xl"></i>
            </a>
            <a href="#" class="text-white mx-2 hover:text-gray-400">
                <i class="bi bi-instagram text-xl"></i>
            </a>

        </div>

        
         <div class="text-center md:text-left">
            <p>&copy; 2024 WOODLAK. All rights reserved. 
                <a href="../orders/terms_and_conditions/termsAndCondition.html" class="text-white hover:text-gray-400 ml-2">Terms and Conditions</a>
            </p>
        </div>

        <!-- Back to Top Button -->
        <div class="mt-4 md:mt-0">
            <a href="#" class="text-white hover:text-gray-400">
                <i class="bi bi-arrow-up-circle-fill text-2xl"></i>
            </a>
        </div>
    </div>
</footer>
</div>
<script>
    function responsive() {
        var x = document.getElementById("content");
        x.classList.toggle("hidden");
    } 
        </script>
    </body>
</html>



<!-- Parent container for the page content and footer -->

