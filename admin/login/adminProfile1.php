<?php
    include 'config.php';
    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
        header('Location: adminLogin.php');
        exit;
    }
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $select = mysqli_query($conn, "SELECT * FROM `admin` WHERE adminID = '$user_id'") or die('Query failed');

        if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
        } else {
            $fetch = null;
        }
    } else {
        $fetch = null;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
        integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="../../resources/css/profile/adminLogin.css">
</head>
<body>
<?php 
$base_url = "http://localhost/woodlak"; 
?>


<header class="bg-[#543310]  h-20 z-50 shadow-lg">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
       
        <div class="flex items-center gap-[1vw]">
            <img class="w-16 h-16 object-contain" src="<?php echo $base_url; ?>../resources/images/Logo.png" alt="Logo">
            <div>
                <h1 class="text-2xl text-white font-bold tracking-wide">WOODLAK</h1>
                <p class="text-sm text-[#D0B8A8]">Admin Panel</p>
            </div>
        </div>

        
        <div class="bg-[#543310] lg:static absolute left-0 lg:min-h-fit min-h-[40vh] top-[9%] lg:w-auto w-full flex items-center px-2 justify-center lg:justify-start xl:contents hidden lg:flex z-40" id="content">
            <ul class="flex lg:flex-row flex-col lg:gap-8 gap-6">
                <li>
                <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/adminProfile1.php">Dashboard</a>

                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/product/product_detail.php">Products</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/orders/view_orders_Admin/OrderList.php">Orders</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/inquiry/inquiries.php">Inquiries</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/payment_process/admin_banktrans_check/admin_panel.php">Bank Transfers</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/login/RegisteredUsers.php">Users</a>
                </li>
            </ul>
        </div>

       
        <div class="flex items-center gap-4">
   
    <a href="adminLogout.php">
        <button class="bg-[#8D653A] text-white px-4 py-2 rounded-full hover:bg-[#D0B8A8] hover:text-[#543310] transition duration-300 shadow-lg">
            Logout
        </button>
    </a>

    <button onclick="responsive()" class="lg:hidden">
        <i class="bi bi-list text-4xl text-white"></i>
    </button>
</div>

    </nav>
</header>

<!-- JS for Responsive Menu -->
<script src="<?php echo $base_url; ?>/resources/JS/navbar.js"></script>

<div class="profile">
    <form action="EditProfile.php" method="get">
        <h1><?php echo $fetch ? htmlspecialchars($fetch['name']) : 'No admin found'; ?></h1>
        <div class="flex">
            <div class="input-roup w-full">
                <span class="span">Name: </span>
                <input type="text" name="profile_name" value="<?php echo $fetch ? htmlspecialchars($fetch['name']) : 'No admin found'; ?>" class="box" readonly style="width:100%;">

                <span>Email: </span>
                <input type="email" name="profile_email" value="<?php echo $fetch ? htmlspecialchars($fetch['email']) : 'No admin found'; ?>" class="box" readonly style="width: 100%;">

                <span>Password: </span>
                <input type="password" id="profile_password" name="profile_password" value="<?php echo $fetch ? htmlspecialchars($fetch['password']) : ''; ?>" class="box" readonly style="width: 100%" class="fas fa-eye eye-icon" id="togglePassword" onclick="togglePassword()">
                <i ></i>
            </div>
        </div>
        <p>Change password? <a href="resetpassword.php">Reset here</a></p>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('profile_password');
        const eyeIcon = document.getElementById('togglePassword');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>
