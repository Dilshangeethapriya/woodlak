<?php
ob_start();
include 'dbcon.php';
session_start();

$MyCode = intval($_REQUEST["PRODUCTC"]);

$sql = "SELECT * FROM product WHERE productID=$MyCode";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $MyProductName =  $row['productName'];
        $MyProductImage = $row['image'];
        $MyProductPrice = $row['price'];
        $MyDescription = $row['description'];
        $MyProductID =  $row['productID'];
    }
} else {
    $MyProductName = "No Product Found...";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>  
    <head>
        <title><?php echo $MyProductName; ?></title>
        <meta charset="utf-8">
		<meta name="veiwport" content="width=device-width,intial-scale=1.0">
        <link rel="stylesheet" href="productViewNew.css">
        <link rel="stylesheet" href="../resources/css/ratingCounts.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        
    </head>
    <body>
        
      <?php include '../includes/navbar.php'; ?>
       
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-2 ">

        <div class="left_side">
            <?php
            echo "<img src=". $MyProductImage." alt=". $MyProductName." class='display mt-4' style='margin-left:15%;'>";
            ?>

   
        </div>

        <div class="right_side">
            <div class="heading">
            <?php
            echo "<h1 class='text-[#C4A484] text-4xl text-center'><b>$MyProductName</b></h1>"; 
            ?>

            </div>
            <div class="description">
            
                <h3 class="text-[#C4A484] text-2xl mb-2"><b>Description</b></h1>
            <?php
                echo "<p  class='text-white'>$MyDescription</p>";
            ?>
            </div>
            <div class="cart">
                <?php
                echo "<p class='text-[#C4A484] text-3xl mb-2 text-center mb-4'><b>Rs.$MyProductPrice</b></p>";
                echo '<form method="POST" action="add_to_cart.php">';
                echo '<input type="hidden" name="productID" value="' . $MyCode . '">';
                echo '<input type="hidden" name="productName" value="' .$MyProductName . '">';
                echo '<input type="hidden" name="price" value="' . $MyProductPrice .'">'; 
                echo '<button type="submit" class="bg-[#78350f] hover:bg-[#5a2b09] text-white rounded-full px-10 py-2 text-l border-2 border-[#78350f] mx-auto mb-4 flex items-center">Add to Cart</button>';
                echo '</form>';
                ?>
            </div>
        </div> 
        </div>
       
         <!-- Review section -->
         <?php include '../reviews/reviews.php' ; ?> 


        <a href="shopping_cart.php" class="shoppingCart bg-[#78350f] hover:bg-white text-white hover:text-[#78350f] fixed bottom-3 right-5  p-3 rounded-full shadow-lg">
        <i class="bi bi-cart4 text-4xl"></i>
        </a>
        <script>
      function responsive() {
        var x = document.getElementById("content");
        x.classList.toggle("hidden");
    }

        </script>
    </body>
</html>