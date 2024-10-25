<?php
include 'dbcon.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

// Add New Product
if (isset($_POST['add'])) {
    $productName = mysqli_real_escape_string($conn,$_POST['productName']);
    $stockLevel = mysqli_real_escape_string($conn, $_POST['stockLevel']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $image = $_FILES['image']['name'];
    $target_dir = "../../resources/images/product/";
    $target_file = $target_dir . basename($image);

    $spinImageUrl = mysqli_real_escape_string($conn,$_POST['spinImageUrl']);
       
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {

        $image_path = "resources/images/product/" . basename($image);
            
        $sql = "INSERT INTO product (productName, stockLevel, description, price, image, spinImageUrl)
                VALUES ('$productName', '$stockLevel', '$description', '$price', '$image_path', '$spinImageUrl')";
            
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

// Update Product Details
if (isset($_POST['update'])) {
    $productID = mysqli_real_escape_string($conn, $_POST['productId']);
    $updateName = mysqli_real_escape_string($conn, $_POST['updateName']);
    $updateDescription = mysqli_real_escape_string($conn, $_POST['updateDescription']);
    $updatePrice = mysqli_real_escape_string($conn, $_POST['updatePrice']);

    $image = $_FILES['updateImage']['name'];

    $updateSpinImageUrl = mysqli_real_escape_string($conn, $_POST['updateSpinImageUrl']);
    
    if (!empty($image)) {
        $target_dir = "../../resources/images/product/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['updateImage']['tmp_name'], $target_file);

        $image_path = "resources/images/product/" . basename($image);

    } else {

        $target_file = $_POST['existingImage'];
        $image_path = str_replace('../../', '', $target_file);
    }
    
        $updateSql = "UPDATE product SET productName='$updateName', description='$updateDescription', price='$updatePrice', image='$image_path', spinImageUrl='$updateSpinImageUrl' WHERE productID='$productID'";
    
    if (mysqli_query($conn, $updateSql)) {
        echo '<script>
        alert("Product updated successfully!");
        window.location.href = "product_detail.php";
        </script>';
    } else {
        echo "Error: " . $updateSql . "<br>" . mysqli_error($conn);
    }    
}

//Update the Stock Level

if (isset($_GET['total'])) {
    $updateProductId = $_GET['updateProductId'];
    $updateQty = $_GET['total'];
    $newQty=$_GET['newQty'];

    $updateStockSql = "UPDATE product SET stockLevel='".$updateQty."' WHERE productID='".$updateProductId."'";
    $updateStockSqlResult = mysqli_query($conn, $updateStockSql);

    if ($updateStockSqlResult === FALSE) {
        die("Error updating data: " .  mysqli_connect_error());
    }else{
        $getNameSql ="SELECT productName FROM product WHERE productID='$updateProductId'";
        $row=mysqli_fetch_assoc(mysqli_query($conn,$getNameSql));
        $stockInSql="INSERT into log_stockIn values (NOW(),'".$updateProductId."','".$row['productName']."','".$newQty."')";
        mysqli_query($conn,$stockInSql);
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="addProduct.css">
        <style>
            body{
                background-image: url('../../resources/images/bg2.png');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover; 
            }
        </style>
    </head>
    <body>
     
        <?php include "../../includes/adminNavbar.php" ?> 

        <div>
            <h1 class="text-center mt-14" style="font-size:50px"><b>Products</b></h1>
            <button class="add_new md:w-[150px] " onclick="openAddForm()" style="background-color:#78350f">+ Add New</button>
            <button class="add_new md:w-[150px] " onclick="openStock()" style="background-color:#78350f">Stocks</button>
            <button class="add_new md:w-[150px] " onclick="openReview()" style="background-color:#78350f">Reviews</button>
        </div>

        <!--Add Form-->

        <div class="form-popup" id="addForm">
            <form action="product_detail.php" method="post" class="form-container" style="background-color: #EADDCA;" enctype="multipart/form-data" onsubmit="return validateImage();">
            <div class="mb-2 flex justify-between items-center">
                <h1><b>Add New Product</b></h1>
                <button type="button" onclick="closeAddForm()"><i class="bi bi-x-circle text-xl"></i></button>
            </div>
            <div class="mb-2">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="productName" required minlength="3" maxlength="20">
            </div>
            <div class="mb-2">
                <label for="stockLevel" class="form-label">Stock Level (Safety stock level is 15)</label>
                <input type="number" min=30 class="form-control" id="stockLevel" name="stockLevel" required title="Minimum stock level is 30">
            </div>
            <div class="mb-2">
                <label for="price" class="form-label">Price (Rs.)</label>
                <input type="number" min=1 step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-2">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required minlength="10" maxlength="450"></textarea>
            </div>
            <div class="mb-2">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image" accept=".jpg, .jpeg, .png" required>
            </div>
            <div class="mb-2">
                <label for="image" class="form-label">Spin Image</label>
                <input type="url" class="form-control" id="spinImageUrl" name="spinImageUrl">
            </div>
                <button type="submit" class="btn" style="background-color:#B99470" name="add">Add Product</button>
            </form>
        </div>

        <!--Update Form-->

        <div class="form-popup" id="updateForm">
            <form action="product_detail.php" method="post" class="form-container" style="background-color: #EADDCA;" enctype="multipart/form-data" onsubmit="return validateUpdateImage();">
            <div class="mb-2 flex justify-between items-center"> 
                <h1><b>Update Product Details</b></h1>
                <button type="button"  onclick="closeUpdateForm()"><i class="bi bi-x-circle text-xl"></i></button>
            </div>
            <div class="mb-2">
                <label for="productId" class="form-label">Product ID</label>
                <input type="text" class="form-control" id="productId" name="productId" readonly>
            </div>
            <div class="mb-2">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="updateName" name="updateName" minlength="3" maxlength="20">
            </div>
            <div class="mb-2">
                <label for="price" class="form-label">Price (Rs.)</label>
                <input type="number" min=0 step="0.01" class="form-control" id="updatePrice" name="updatePrice">
            </div>
            <div class="mb-2 flex justify-between items-center">
                <label for="stockLevel" class="form-label">Stock Level</label>
                <button type="button" class="text-l bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[50%] rounded" onclick="openStockForm()">Update Stocks</button>
            </div>
            <div class="mb-2">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="updateDescription" name="updateDescription" rows="3" minlength="10" maxlength="450"></textarea>
            </div>
            <div class="mb-2">
                <div class="flex justify-between items-center">
                <label for="updateImage" class="form-label">Product Image</label>
                <img id="currentImage" src="" alt="Current Product Image" class="w-16">
                </div>
                <input type="file" class="form-control" id="updateImage" name="updateImage" accept=".jpg, .jpeg, .png">
                <input type="hidden" id="existingImage" name="existingImage"> <!-- Hidden input to store existing image path -->
            </div>
            <div class="mb-2">
                <label for="updateSpinImageUrl" class="form-label">Spin Image</label>
                <input type="url" class="form-control" id="updateSpinImageUrl" name="updateSpinImageUrl">
            </div>
                <button type="submit" class="btn" style="background-color:#B99470" name="update">Update Product</button>
            </form>
        </div>

        <!--Update Stock Form-->

        <div class="form-popup w-[30%]" style="margin-right:345px; margin-bottom:70px" id="stockForm" >
            <form action="product_detail.php" method="post" class="form-container" style="background-color: #EADDCA;" enctype="multipart/form-data">
            <div class="mb-2 flex justify-between items-center">
                <h1 class="text-center"><b>Update Stock Level</b></h1>
                <button type="button" onclick="closeStockForm()"><i class="bi bi-x-circle text-xl"></i></button> 
            </div>
            <div class="mb-2">
                <label for="stockLevel" class="form-label">Stock Level</label>
                <input type="number" class="form-control" id="current" name="current" readonly>
                <input type="hidden" class="form-control" id="productId" name="productId">
            </div>
            <div class="mb-2">
                <label for="stockLevel" class="form-label">New Stock</label>
                <input type="number" min=15 class="form-control" id="newStock" name="newStock" required>
            </div>
            <div class="mb-2 flex justify-between items-center">
                <label for="stockLevel" class="form-label">Total</label>
                <input type="number" class="form-control w-[60%]" id="total" name="total"  readonly>
                <button type="button" class="text-l bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-[30%] rounded" onclick="calTotal()">Calculate</button>

            </div>
                <button type="button" class="btn" style="background-color:#B99470" onclick="updateQuantity()" name="updateStock">Update Stock Quantity</button>
                
            </form>
        </div>



        <div class="mt-4" style="padding: 20px;">
        <table class="table table-responsive table-hover table-striped" style="border-collapse: separate; border-spacing: 0; border: 2px solid #eaddca; border-radius: 12px; overflow: hidden;">
            <thead class="table-light" >
                <tr>
                <th scope="col" style="background-color: #dbc5a4;">Product ID</th>
                <th scope="col" style="background-color: #dbc5a4;">Product Name</th>
                <th scope="col" style="background-color: #dbc5a4;">Price</th>
                <th scope="col" style="background-color: #dbc5a4;">Stock Level</th>
                <th scope="col" style="background-color: #dbc5a4;">Description</th>
                <th scope="col" style="background-color: #dbc5a4;">Image</th>
                <th scope="col" style="background-color: #dbc5a4;">Spin Model</th>
                <th scope="col" style="text-align:center; background-color:#dbc5a4;">Action</th>
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
                        echo "<td class='w-2'><img class='w-16' src='../../{$row['image']}'></td>";
                        if (isset($row['spinImageUrl']) && !empty($row['spinImageUrl'])) {
                            echo "<td class='w-2'><a class='w-16' href='{$row['spinImageUrl']}' style='color: blue; text-decoration: underline;'>{$row['productName']}</a></td>";
                        } else {
                            echo "<td class='w-2'></td>";
                        }
                        echo "<td class='w-2'><div class='flex flex-col md:flex-row justify-between'><button class='text-l bg-[#6f5843] hover:bg-[#5c4a38] text-white p-2 w-full md:w-[48%] rounded' onclick=\"openUpdateForm('{$row['productID']}','{$row['productName']}','{$row['price']}','{$row['description']}','../../{$row['image']}','{$row['spinImageUrl']}','{$row['stockLevel']}')\">Update</button>&nbsp;<button class='text-l bg-[#e50000] hover:bg-[#ff0000] text-white p-2 w-full md:w-[48%] rounded' onclick=\"openDeleteForm('{$row['productID']}')\">Delete</button></div></td>";
                        echo "</tr>";
                    }
                }
                mysqli_close($conn);
                ?>
                </tbody>
            </table>
        </div>
       
        <script>
            function openAddForm() {
                document.getElementById("addForm").style.display = "block";
            }

            function closeAddForm() {
                document.getElementById("addForm").style.display = "none";
            }

            function openDeleteForm(productID) {
                if (confirm("Are you sure you want to delete this entry?")) {
                    window.location.href = "product_detail.php?deleteProductId=" + productID ;
                }
            }

            function openStock(){
                window.location.href = "stockReport.php" ;
            }

            function openReview(){
                window.location.href = "../reviews/reviews.php" ;
            }

            function openUpdateForm(productID,name,price,description,image,spinImageUrl,stock){
                document.getElementById("updateForm").style.display = "block";
                document.querySelector("input[name='productId']").value=productID;
                document.querySelector("input[name='updateName']").value=name;
                document.querySelector("input[name='updatePrice']").value=price;
                document.querySelector("textarea[name='updateDescription']").value=description;
                document.getElementById("existingImage").value = image;
                document.getElementById("currentImage").src = image;
                document.querySelector("input[name='current']").value=stock;
                document.querySelector("input[name='updateSpinImageUrl']").value=spinImageUrl;

            }

            function closeUpdateForm() {
                document.getElementById("updateForm").style.display = "none";
                document.getElementById("stockForm").style.display = "none";
            }

            function openStockForm(productID,name,price,description,image){
                document.getElementById("stockForm").style.display = "block";

            }

            function closeStockForm() {
                document.getElementById("stockForm").style.display = "none";
                document.querySelector("input[name='newStock']").value="";
                document.querySelector("input[name='total']").value="";
            }

            function calTotal(){
                const current=parseInt(document.getElementById("current").value);
                const newStock=parseInt(document.getElementById("newStock").value);
                var total=current+newStock;
                document.querySelector("input[name='total']").value=total;

            }

            function updateQuantity(){
                const productId=document.getElementById("productId").value;
                var totalValue=document.getElementById("total").value;
                const newStock=document.getElementById("newStock").value;
                console.log(total);
                if (totalValue === "" || isNaN(totalValue)) {
                    alert("The total is not calculated");
                }else{
                    var total=parseInt(totalValue);
                    if(confirm("Update the Stock Level")){
                    window.location.href = "product_detail.php?updateProductId=" + productId + "&total="+total + "&newQty="+newStock;
                }
                }
                
            }

            function validateImage() {
                const fileInput = document.getElementById('image');
                const file = fileInput.files[0];
                
                if (file) {
                    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
                    const maxSize = 2 * 1024 * 1024;

                    if (!allowedExtensions.exec(file.name)) {
                        alert('Please upload a file with a valid image format (jpg, jpeg, png).');
                        fileInput.value = '';
                        return false;
                    }

                    if (file.size > maxSize) {
                        alert('File size must be less than 2MB.');
                        fileInput.value = '';
                        return false;
                    }
                }

                return true;
            }

            function validateUpdateImage() {
                const fileInput = document.getElementById('updateImage');
                const file = fileInput.files[0];
                
                if (file) {
                    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
                    const maxSize = 2 * 1024 * 1024;

                    if (!allowedExtensions.exec(file.name)) {
                        alert('Please upload a file with a valid image format (jpg, jpeg, png).');
                        fileInput.value = '';
                        return false;
                    }

                    if (file.size > maxSize) {
                        alert('File size must be less than 2MB.');
                        fileInput.value = '';
                        return false;
                    }
                }

                return true;
            }

        </script>
    </body>
    

</html>