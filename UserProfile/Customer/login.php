<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $select = mysqli_query($conn, "SELECT * FROM `customer` WHERE email = '$email' AND password = '$password'") or die('query failed');

    if(mysqli_num_rows($select) > 0){
        $row = mysqli_fetch_assoc($select);
        $_SESSION['user_id'] = $row['customerID'];
        $_SESSION['user_name'] = $row['name']; 
        header('Location: ../../index.php');
        exit();
    }else{
        $message[] = ' Incorrect email or password ';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../resources/css/profile/profile1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
            body{
                font-family: sans-serif;
            }
        </style>
</head>
<body>
<header class="bg-[#543310] h-20">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
        <div class="flex items-center gap-[1vw]">
            <img class="w-16" src="Logo.png" alt="Logo">
            <h1 class="text-xl text-white font-sans"><b>WOODLAK</b></h1>
        </div>
        <div class="lg:static absolute bg-[#543310] lg:min-h-fit min-h-[39vh] left-0 top-[9%] lg:w-auto w-full flex items-center px-5 justify-center lg:justify-start text-center lg:text-right xl:contents hidden lg:flex" id="content">
            <ul class="flex lg:flex-row flex-col lg:gap-[4vw] gap-8">
                <li>
                    <a class="text-white hover:text-[#D0B8A8] " href="../../index.php">Home</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../../inquirymgt/contactUs.php">Contact Us</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../../aboutUs/About_Us.php">About Us</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../../product/product_catalog.php">Products</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../../orders/Tracking_page/Order_tracking.php">Orders</a>
                </li>
            </ul>
        </div>
        <?php 
        if(isset($_SESSION['user_name'])) {
            $user_name = $_SESSION['user_name'];
        ?>
        <div class="flex items-center gap-3">
           
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='register.php'">Register</button>
        </div>
        <?php } else { ?>
        <div class="flex items-center gap-3">
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='register.php'">Register</button>
          
            <button onclick="responsive()"><i class="bi bi-list text-4xl lg:hidden text-white"></i></button>
        </div>
        <?php } ?>
    </nav>
</header>
    <div class="register-container">
        <form action="login.php" method="POST" class="register-form" enctype="multipart/form-data">
            <h2 class="login-header">LOGIN</h2>
            <?php

            if(isset($message)){
                foreach($message as $message){
                    echo '<div class="message">'.$message.'</div>';
                }
            }
            ?>

            
            <div class="input-group">
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            
            <button type="submit" name="submit" class="btn text-lg">Login Now</button>
            
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
            <p>Login as Admin     <a href="../../admin/login/adminLogin.php">Login Here</a></p>
        </form>
    </div>
    <?php include "includes/footer.php" ?>
    <script src="../../resources/JS/navbar.js"> </script>
</body>
</html>