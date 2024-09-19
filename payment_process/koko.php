<?php
session_start();

$_SESSION['paymentMethod'] = "Koko";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koko Payment Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4ede8;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">





    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center">
            <img src="pictures/koko.png" alt="Koko" class="mx-auto mb-4">
            <p class="text-gray-700">Koko is a smart payment gateway that allows you to pay in 3 equal, interest-free installments.</p>
            <p class="text-gray-500 mt-2">No credit card required. No hidden costs.</p>
        </div>

        <div class="mt-6 text-center">
            <p class="text-lg font-semibold">Your order total value is</p>
            <p class="text-3xl font-bold text-green-500">Rs 4,700.00</p>
        </div>

        <div class="mt-6">
            <div class="flex justify-between items-center">
                <div class="text-center">
                    <p class="text-gray-600">Rs 1,566.67</p>
                    <p class="text-gray-400 text-sm">Pay now</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600">Rs 1,566.67</p>
                    <p class="text-gray-400 text-sm">in 30 days</p>
                    <p id="date30" class="text-gray-400 text-sm"></p> <!-- 30-day date will be here -->
                </div>
                <div class="text-center">
                    <p class="text-gray-600">Rs 1,566.67</p>
                    <p class="text-gray-400 text-sm">in 60 days</p>
                    <p id="date60" class="text-gray-400 text-sm"></p> <!-- 60-day date will be here -->
                </div>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-gray-600 text-center">To complete your purchase enter your mobile number</p>
            <div class="flex mt-4">
                <span class="flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">+94</span>
                <input type="text" id="mobileNumber" class="w-full p-2 border border-gray-300 rounded-r-md focus:outline-none" placeholder="Enter your mobile number">
            </div>
        </div>

        <div id="passwordSection" class="mt-4 hidden">
            <p class="text-gray-600 text-center">Enter your password</p>
            <input type="password" id="password" class="w-full p-2 mt-2 border border-gray-300 rounded-md focus:outline-none" placeholder="Enter your password">
        </div>

        <div class="mt-6">
            <button id="continueButton" class="w-full py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition" onclick="handleContinue()">CONTINUE</button>
        </div>

        <div class="mt-4 text-center text-gray-500 text-sm">
            <p>I agree to the Koko <a href="#" class="text-blue-500 underline">terms</a> and to receive these <a href="#" class="text-blue-500 underline">terms electronically</a>.</p>
        </div>
    </div>

    <script>
        function addDays(date, days) {
            const result = new Date(date);
            result.setDate(result.getDate() + days);
            return result;
        }

        // Format date as 'D M'
        function formatDate(date) {
            const options = { day: 'numeric', month: 'short' };
            return date.toLocaleDateString('en-US', options);
        }

        // Get today's date
        const today = new Date();

        // Calculate the dates
        const date30 = addDays(today, 30);
        const date60 = addDays(today, 60);

        // Update the HTML with the calculated dates
        document.getElementById('date30').textContent = formatDate(date30);
        document.getElementById('date60').textContent = formatDate(date60);

        // Function to handle the continue button click
        function handleContinue() {
            const mobileNumber = document.getElementById('mobileNumber').value;
            const passwordSection = document.getElementById('passwordSection');
            const continueButton = document.getElementById('continueButton');

            if (mobileNumber === '') {
                alert("Please enter your mobile number.");
                return;
            }

            if (!passwordSection.classList.contains('hidden')) {
                const password = document.getElementById('password').value;
                if (password === '') {
                    alert("Please enter your password.");
                    return;
                }

                // Redirect to payment invoice page after changing button text
                continueButton.textContent = "Pay Now!";
                setTimeout(() => {
                    window.location.href = "payment_invoice.php";
                }, 1000);

            } else {
                // Display the password input and change button text
                passwordSection.classList.remove('hidden');
                continueButton.textContent = "Pay Now!";
            }
        }
    </script>

</body>
</html>
