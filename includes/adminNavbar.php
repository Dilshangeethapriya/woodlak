<?php 
$base_url = "http://localhost/woodlak"; 
?>


<header class="bg-[#543310]  h-20 z-50 shadow-lg">
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
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/dashboard.php">Dashboard</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/products/product_detail.php">Products</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/orders/view_orders_Admin/OrderList.php">Orders</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/inquiry/inquiries.php">Inquiries</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/admin_banktrans_check/admin_panel.php">Bank Transfers</a>
                </li>
                <li>
                    <a class="text-[#B0C186] hover:text-[#D0B8A8] transition duration-300 font-medium text-lg" href="<?php echo $base_url; ?>/admin/login/RegisteredUsers.php">Users</a>
                </li>
            </ul>
        </div>

       
        <div class="flex items-center gap-4">
            <button class="bg-[#8D653A] text-white px-4 py-2 rounded-full hover:bg-[#D0B8A8] hover:text-[#543310] transition duration-300 shadow-lg" onclick="location='../admin/login/adminProfile1.php'">
                Profile
            </button> 
            <button onclick="responsive()" class="lg:hidden">
                <i class="bi bi-list text-4xl text-white"></i>
            </button>
        </div>
    </nav>
</header>

<!-- JS for Responsive Menu -->
<script src="<?php echo $base_url; ?>/resources/JS/navbar.js"></script>
