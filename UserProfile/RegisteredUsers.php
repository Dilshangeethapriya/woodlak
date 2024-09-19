<?php
    include 'config.php';


    if(isset($_SESSION['message'])){
        $message = $_SESSION['message'];
        $message_type = $_SESSION['message_type'];
        
        echo '<div class="alert alert-' . ($message_type == 'success' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registered Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="Admin">
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
                    <a class="text-white  hover:text-[#D0B8A8]" href="../product/product_detail.php">Products</a>
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

<h1 class="Users text-white text-center text-3xl mt-32 mb-10" >Registered Users</h1>
<style>
     
        .input-roup {
            position: relative;
        }
    </style>
<table class="table ">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Name</th>
      <th scope="col">Email</th>
      <th scope="col">Contact</th>
      <th scope="col">Gender</th>
      <th scope="col">Postal Code</th>
      <th scope="col">House No</th>
      <th scope="col">Street Name</th>
      <th scope="col">City</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $query="SELECT * FROM `customer`" ;
    $query_run = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($query_run)>0){
        foreach($query_run as $row){
            ?>
            <tr>
                
                <td><?=$row['customerID'];   ?></td>
                <td><?=$row['name'];   ?></td>
                <td><?=$row['email'];   ?></td>
                
                <td><?=$row['contact'];   ?></td>
                <td><?=$row['gender'];   ?></td>
                <td><?=$row['postalCode'];   ?></td>
                <td><?=$row['houseNo'];   ?></td>
                <td><?=$row['streetName'];   ?></td>
                <td><?=$row['city'];   ?></td>
                <td><form action="delete.php" method="POST" style="display:inline;">
                        <input type="hidden" name="customerID" value="<?=$row['customerID'];?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form></td>
             </tr>
            <?php
        }
    }else{
     ?>
        <tr>
            <td colspan="6">No record Found</td>
        </tr>
     <?php

    }
    ?>
    
    
  </tbody>
</table>
</body>
</html>