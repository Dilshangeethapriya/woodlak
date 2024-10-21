<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $select = mysqli_query($conn, "SELECT * FROM `admin` WHERE email = '$email'") or die('Query failed');
    
    if (mysqli_num_rows($select) > 0) {
        $row = mysqli_fetch_assoc($select);
        
        if ($password == $row['password']) {  
            $_SESSION['user_id'] = $row['adminID']; 
            
            header('Location: ../dashboard.php');
            exit;
        } else {
            $message[] = 'Incorrect password';
        }
    } else {
        $message[] = 'Email not found';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: sans-serif;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php 
$base_url = "http://localhost/woodlak"; 
?>

<header class="bg-[#543310] h-20 z-50 shadow-lg">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
       
        <div class="flex items-center gap-[1vw]">
            <img class="w-16 h-16 object-contain" src="<?php echo $base_url; ?>/resources/images/Logo.png" alt="Logo">
            <div>
                <h1 class="text-2xl text-white font-bold tracking-wide">WOODLAK</h1>
                <p class="text-sm text-[#D0B8A8]">Admin Panel</p>
            </div>
        </div>

        <div class="bg-[#543310] lg:static absolute left-0 lg:min-h-fit min-h-[40vh] top-[9%] lg:w-auto w-full flex items-center px-2 justify-center lg:justify-start xl:contents hidden lg:flex z-40" id="content">
            <ul class="flex lg:flex-row flex-col lg:gap-8 gap-6">
                <li><a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/dashboard.php">Dashboard</a></li>
                <li><a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/product/product_detail.php">Products</a></li>
                <li><a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/orders/view_orders_Admin/OrderList.php">Orders</a></li>
                <li><a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/inquiry/inquiries.php">Inquiries</a></li>
                <li><a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/payment_process/admin_banktrans_check/admin_panel.php">Bank Transfers</a></li>
                <li><a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/UserProfile/Admin/RegisteredUsers.php">Users</a></li>
            </ul>
        </div>

        <div class="flex items-center gap-4"></div>
    </nav>
</header>

<script src="<?php echo $base_url; ?>/resources/JS/navbar.js"></script>

<div class="register-container">
    <form action="adminLogin.php" method="POST" class="register-form" onsubmit="return validateForm()" enctype="multipart/form-data">
        <h2>ADMIN LOGIN</h2>
        <?php
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '<div class="message">' . $msg . '</div>';
            }
        }
        ?>
        
        <div class="input-group">
            <input type="email" name="email" placeholder="Enter Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Enter Password" required>
            <span class="error-message" id="passwordError"></span>
        </div>
        
        <button type="submit" name="submit" class="btn">Login Now</button>
        <p>Change your password? <a href="resetpassword.php">Reset here</a></p>
    </form>
</div>

<script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const passwordError = document.getElementById('passwordError');
        const passwordRegex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

        if (!passwordRegex.test(password)) {
            passwordError.textContent = 'Password must be at least 8 characters long, contain at least one number and one special character.';
            return false;
        } else {
            passwordError.textContent = '';
            return true;
        }
    }
</script>

</body>
</html>
