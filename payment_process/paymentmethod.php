<?php
session_start();

$_SESSION['paymentMethod'] = "Cash On Delivery";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods</title>

    <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
   
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
 
    <style>
        .bg-neemwood {
            background-color: #a67b5b; 
        }
    </style>
</head>
<body class="bg-neemwood min-h-screen">

    
<?php include '../includes/navbar.php'; ?>

   
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md mx-auto mt-10">
        <h2 class="text-xl md:text-2xl font-semibold text-center mb-6">Select Your Payment Method</h2>
        <div class="space-y-4">
           
            <a href="credit-card.php" class="flex items-center p-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                <img src="pictures/creditcard.jpg" alt="Credit/Debit Card" class="w-12 h-12 object-contain mr-4">
                <span class="text-gray-700 font-medium">Credit/Debit Card</span>
            </a>

    
            <a href="bank-transfer.php" class="flex items-center p-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                <img src="pictures/banktrans.webp" alt="Bank Transfer" class="w-12 h-12 object-contain mr-4">
                <span class="text-gray-700 font-medium">Bank Transfer</span>
            </a>

            
            <div id="cod-option" onclick="selectCOD()" class="flex items-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 transition">
                <img src="pictures/cod.webp" alt="Cash on Delivery" class="w-12 h-12 object-contain mr-4">
                <span class="text-gray-700 font-medium">Cash on Delivery</span>
            </div>

            <a href="koko/koko.php" class="flex items-center p-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                <img src="pictures/koko.png" alt="Koko (BNPL)" class="w-12 h-12 object-contain mr-4">
                <span class="text-gray-700 font-medium">Koko (Buy Now Pay Later!)</span>
            </a>
        </div>

  
        <div id="confirm-btn-container" class="mt-6 hidden">
            <button onclick="confirmCOD()" class="w-full py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                Confirm Cash on Delivery
            </button>
        </div>
    </div>

    <script>
        function responsive() {
            var x = document.getElementById("content");
            if (x.classList.contains("hidden")) {
                x.classList.remove("hidden");
            } else {
                x.classList.add("hidden");
            }
        }

        function selectCOD() {
            const codOption = document.getElementById('cod-option');
            const confirmBtnContainer = document.getElementById('confirm-btn-container');

          
            codOption.classList.toggle('bg-green-100');
            codOption.classList.toggle('border');
            codOption.classList.toggle('border-green-500');

        
            confirmBtnContainer.classList.toggle('hidden');
        }

        function confirmCOD() {
           
            window.location.href = 'payment_invoice.php';
           
        }



        function responsive() {
                var x = document.getElementById("content");
                if (x.classList.contains("hidden")) {
                    x.classList.remove("hidden");
                } else {
                    x.classList.add("hidden");
                }
            }
    </script>

</body>
</html>
