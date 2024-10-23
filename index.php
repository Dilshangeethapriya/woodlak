<?php
session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Woodlak Neem Combs</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="resources/css/index.css">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body>
    <?php include 'includes/navbar.php'; ?>

<section class="feature-section text-center mt-20 mx-4">
    <h2 class="text-4xl font-semibold header-title mb-5">Why Choose Our Neem Combs?</h2>
    <div class="flex flex-col md:flex-row justify-center gap-8">
        
        <div class="md:w-[30%] w-full">
            <i class="bi bi-flower3 text-5xl mb-3"></i>
            <h3 class="text-2xl font-semibold subheading">Natural Benefits</h3>
            <p>Neem has natural antibacterial properties that promote a healthy scalp and reduce dandruff.</p>
        </div>
        
        <div class="md:w-[30%] w-full">
            <i class="bi bi-gem text-5xl mb-3"></i>
            <h3 class="text-2xl font-semibold subheading">Durability</h3>
            <p>Our combs are strong and long-lasting, crafted from premium neem wood for everyday use.</p>
        </div>
        
        <div class="md:w-[30%] w-full">
            <i class="bi bi-recycle text-5xl mb-3"></i>
            <h3 class="text-2xl font-semibold subheading">Eco-Friendly</h3>
            <p>Our combs are 100% biodegradable, making them a sustainable choice for your daily hair care routine.</p>
        </div>
        
    </div>
</section>

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-10 px-4 w-5/6 mx-auto">


<a href="product/product_catalog.php" class="sm:h-72 md:h-80 lg:h-96 sm:my-20 md:my-10"> 
    <div class="comb-card bg-[#543310] text-center rounded-lg shadow-lg p-3 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 h-96">
        <img src="resources/images/Curly_Hair.jpg" alt="Curly Hair Comb" class="w-full h-56 rounded-t-lg object-cover">
        <h3 class="mt-3 text-2xl header-title">Curly Hair Neem Comb</h3>
        <p class="mt-3 text-[#E2D1C3]">Perfect for defining curls and reducing frizz naturally.</p> 
    </div>
</a>


<a href="product/product_catalog.php" class="sm:h-72 md:h-80 lg:h-96 sm:my-20 md:my-10">
    <div class="comb-card bg-[#543310] text-center rounded-lg shadow-lg p-3 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 h-96">
        <img src="resources/images/Straight_Hair.png" alt="Straight Hair Comb" class="w-full h-56 rounded-t-lg object-cover">
        <h3 class="mt-3 text-2xl header-title">Straight Hair Neem Comb</h3>
        <p class="mt-3 text-[#E2D1C3]">For smooth and tangle-free hair, with gentle detangling action.</p> 
    </div>
</a>


<a href="product/product_catalog.php" class="sm:h-72 md:h-80 lg:h-96 sm:my-20 md:my-10 md:col-span-2 lg:col-span-1 md:flex md:mx-auto  md:w-2/4 lg:w-full">
    <div class="comb-card bg-[#543310] text-center rounded-lg shadow-lg p-3 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 h-96">
        <img src="resources/images/Thick_Hair.jpg" alt="Thick Hair Comb" class="w-full h-56 rounded-t-lg object-cover">
        <h3 class="mt-3 text-2xl header-title">Thick Hair Neem Comb</h3>
        <p class="mt-3 text-[#E2D1C3]">Ideal for thick hair, providing a firm yet smooth combing experience.</p> 
    </div>
</a>

</section>

<?php include 'includes/footer.php' ?>


        <script src="resources/JS/navbar.js"></script>
    </body>
</html>
