<?php
include 'dbcon.php';

// Add New Product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = mysqli_real_escape_string($conn,$_POST['productName']);
    $stockLevel = mysqli_real_escape_string($conn, $_POST['stockLevel']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    $image = $_FILES['image']['name'];
    $target_dir = "Images/";
    $target_file = $target_dir . basename($image);
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO product (productName, stockLevel, description, price, image)
                VALUES ('$productName', '$stockLevel', '$description', '$price', '$target_file')";
        
        if (mysqli_query($conn, $sql)) {
            echo '<script>
            alert("New product added successfully!");
            window.location.href = "product_detail.php";
            </script>';
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Error uploading image.";
    }  
}

//Fetch Product Details

$fetchSql = "SELECT * FROM product";
$result = mysqli_query($conn, $fetchSql);

if ($result === FALSE) {
    die("Error fetching data: " . mysqli_connect_error());
}

//Delete Product Details

if (isset($_GET['deleteProductId'])) {
    $deleteProductId = $_GET['deleteProductId'];

    $deleteSql = "DELETE FROM product WHERE productID='".$deleteProductId."'";
    $deleteResult = mysqli_query($conn, $deleteSql);

    if ($deleteResult === FALSE) {
        die("Error deleting data: " .  mysqli_connect_error());
    }

  
    header("Location: product_detail.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
        <title>Product Details</title>
        <meta charset="utf-8">
		<meta name="veiwport" content="width=device-width,intial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="addProduct.css">
        <style>
            body{
                background : #c19a6b;
            }

        </style>
    </head>
    <body>
    <header class="bg-[#543310] h-20 fixed w-full top-0 mt-0">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
        <div class="flex items-center gap-[1vw]">
            <img class="w-16" src="Logo.png" alt="Logo">
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

<script>
    function responsive() {
        var x = document.getElementById("content");
        x.classList.toggle("hidden");
    }
</script>



        <div>
        <h1 class="text-center mt-32" style="font-size:50px"><b>Products</b></h1>
        <button class="add_new" onclick="openForm()" style="background-color:#78350f">+ Add New</button>
        </div>
        <div class="form-popup" id="myForm">
            <form action="product_detail.php" method="post" class="form-container" enctype="multipart/form-data">
                <h1><b>Add New Product</b></h1>
            <div class="mb-2">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="productName" required>
            </div>
            <div class="mb-2">
                <label for="stockLevel" class="form-label">Stock Level</label>
                <input type="number" class="form-control" id="stockLevel" name="stockLevel" required>
            </div>
            <div class="mb-2">
                <label for="price" class="form-label">Price (Rs.)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-2">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-2">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>

                <button type="submit" class="btn" style="background-color:#B99470">Add Product</button>
                <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
            </form>
        </div>

        <div class="mt-4">
        <table class="table table-responsive">
            <thead class="table-light">
                <tr>
                <th scope="col">Product ID</th>
                <th scope="col">Product Name</th>
                <th scope="col">Price</th>
                <th scope="col">Stock Level</th>
                <th scope="col">Description</th>
                <th scope="col">Image</th>
                <th scope="col" style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td class='w-2'>{$row['productID']}</td>";
                        echo "<td class='w-2'>{$row['productName']}</td>";
                        echo "<td class='w-2'>{$row['price']}</td>";
                        echo "<td class='w-2'>{$row['stockLevel']}</td>";
                        echo "<td class='w-20' colspan='1'>{$row['description']}</td>";
                        echo "<td class='w-2'><img class='w-16' src={$row['image']}></td>";
                        echo "<td class='w-2'><button class='btn btn-warning'>Update</button>&nbsp;<button class='btn btn-danger' onclick=\"openDeleteForm('{$row['productID']}')\">Delete</button></td>";
                        echo "</tr>";
                    }
                }
                mysqli_close($conn);
                ?>
                </tbody>
            </table>
        </div>

      
       
        <script>
            function openForm() {
                document.getElementById("myForm").style.display = "block";
            }

            function closeForm() {
                document.getElementById("myForm").style.display = "none";
            }

            function openDeleteForm(productID) {
            if (confirm("Are you sure you want to delete this entry?")) {
                window.location.href = "product_detail.php?deleteProductId=" + productID ;
            }
        }
        </script>
    </body>
    

</html>