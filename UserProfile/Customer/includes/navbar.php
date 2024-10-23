<?php 
$base_url = "http://localhost/woodlak"; 

?>

<header class="bg-[#543310] h-20 top-0 w-full z-50">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
        <div class="flex items-center gap-[1vw]">
            <img class="w-16" src="<?php echo $base_url; ?>/resources/images/Logo.png" alt="Logo">
            <h1 class="text-xl text-white font-sans"><b>WOODLAK</b></h1>
        </div>

        <!-- Desktop Menu -->
        <div class="lg:flex hidden" id="content">
            <ul class="flex lg:flex-row flex-col lg:gap-[4vw] gap-8  z-50">
                <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/index.php">Home</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/inquirymgt/contactUs.php">Contact Us</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/aboutUs/About_Us.php">About Us</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/product/product_catalog.php">Products</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="<?= $base_url;?>/orders/Tracking_page/order_tracking.php">Orders</a></li>
            </ul>
        </div>

        <!-- Profile or Login/Register -->
        <div class="flex items-center gap-3">
            <?php if(isset($_SESSION['user_name'])) { ?>
                <span class="mr-4 text-lg text-white"><?php echo $_SESSION['user_name']; ?></span>
                <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='<?php echo $base_url; ?>/Userprofile/customer/profile.php'">Profile</button>
            <?php } else { ?>
                <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='<?php echo $base_url; ?>/Userprofile/register.php'">Register</button>
                <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='<?php echo $base_url; ?>/Userprofile/login.php'">Login</button>
            <?php } ?>
            <button onclick="responsiveMenu()" class="lg:hidden">
                <i class="bi bi-list text-4xl text-white"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden absolute w-full lg:hidden bg-[#543310] ">
        <ul class="flex flex-col items-center gap-4 py-4 z-50">
            <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/index.php">Home</a></li>
            <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/inquirymgt/contactUs.php">Contact Us</a></li>
            <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/aboutUs/About_Us.php">About Us</a></li>
            <li><a class="text-white hover:text-[#D0B8A8]" href="<?php echo $base_url; ?>/product/product_catalog.php">Products</a></li>
            <li><a class="text-white hover:text-[#D0B8A8]" href="<?= $base_url;?>/orders/Tracking_page/order_tracking.php">Orders</a></li>
        </ul>
    </div>
</header>

<script>
    function responsiveMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
        } else {
            mobileMenu.classList.add('hidden');
        }
    }
</script>

<!-- Include Tailwind and other resources if needed -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="../../../resources/JS/navbar.js"></script>
