<?php
session_start();


$_SESSION['paymentMethod'] = "Koko";
$totalprice = $_SESSION['totalPrice'];
$installments = number_format($totalprice / 3, 2); 
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
            background: url('../../resources/images/bg4.png');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover;
        }
        .button-fade {
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        .my-hidden {
            opacity: 0;
            visibility: hidden;
        }
        .my-visible {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>

<?php include '../../includes/navbar.php'; ?>

<div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center">
            <img src="../pictures/koko.png" alt="Koko" class="mx-auto mb-4">
            <p class="text-gray-700">Koko is a smart payment gateway that allows you to pay in 3 equal, interest-free installments.</p>
            <p class="text-gray-500 mt-2">No credit card required. No hidden costs.</p>
        </div>

        <div class="mt-6 text-center">
            <p class="text-lg font-semibold">Your order total value is</p>
            <p class="text-3xl font-bold text-green-500">Rs <?php echo $totalprice; ?>.00</p>
        </div>

        <div class="mt-6">
            <div class="flex justify-between items-center">
                <div class="text-center">
                    <p class="text-gray-600">Rs <?php echo $installments; ?></p>
                    <p class="text-gray-400 text-sm">Pay now</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600">Rs <?php echo $installments; ?></p>
                    <p class="text-gray-400 text-sm">in 30 days</p>
                    <p id="date30" class="text-gray-400 text-sm"></p> 
                </div>
                <div class="text-center">
                    <p class="text-gray-600">Rs <?php echo $installments; ?></p>
                    <p class="text-gray-400 text-sm">in 60 days</p>
                    <p id="date60" class="text-gray-400 text-sm"></p> 
                </div>
            </div>
        </div>

        
        <div class="mt-6">
            <p class="text-gray-600 text-center">To complete your purchase, enter your mobile number</p>
            <div class="flex mt-4">
                <span class="flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">+94</span>
                <input type="text" id="mobileNumber" name="mobileNumber" class="w-full p-2 border border-gray-300 rounded-r-md focus:outline-none" placeholder="Enter your mobile number">
            </div>
        </div>

   
        <div id="passwordSection" class="my-hidden hidden">
            <p class="text-gray-600 text-center">Enter the OTP sent to your phone</p>
            <input type="text" id="otp" class="w-full p-2 mt-2 border border-gray-300 rounded-md focus:outline-none" placeholder="Enter your OTP">
        </div>

       
        <div class="mt-6">
            <button id="continueButton" class="w-full py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">CONTINUE</button>
            <button id="verifyButton" class="w-full py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition button-fade my-hidden">VERIFY</button>
        </div>

        <div class="mt-4 text-center text-gray-500 text-sm">
            <p>I agree to the Koko <a href="#" class="text-blue-500 underline">terms</a> and to receive these <a href="#" class="text-blue-500 underline">terms electronically</a>.</p>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
<script>

function formatDate(date) {
    const options = { day: 'numeric', month: 'short' };
    return date.toLocaleDateString('en-US', options);
}


function addDays(date, days) {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}


const today = new Date();
const date30 = addDays(today, 30);
const date60 = addDays(today, 60);


document.getElementById('date30').textContent = formatDate(date30);
document.getElementById('date60').textContent = formatDate(date60);


document.getElementById('continueButton').addEventListener('click', function () {
    const mobileNumber = document.getElementById('mobileNumber').value;

    if (mobileNumber && document.getElementById('passwordSection').classList.contains('my-hidden')) {
 
        this.disabled = true;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "send_sms.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {

                document.getElementById('passwordSection').classList.remove('my-hidden');
                document.getElementById('passwordSection').classList.toggle('hidden');
                document.getElementById('continueButton').classList.add('my-hidden');
                const verifyButton = document.getElementById('verifyButton');
                verifyButton.classList.remove('my-hidden');
                verifyButton.classList.add('my-visible');
            } else {
                alert("Failed to send OTP. Please try again.");

                document.getElementById('continueButton').disabled = false;
            }
        };


        xhr.send("mobileNumber=" + "+94" + mobileNumber);
    } else if (!mobileNumber) {
        alert("Please enter your mobile number.");
    }
});


document.getElementById('verifyButton').addEventListener('click', function () {
    verifyOTP();
});


document.getElementById('otp').addEventListener('keyup', function(event) {
    if (event.key === 'Enter') { 
        verifyOTP();
    }
});

function verifyOTP() {
    const enteredOTP = document.getElementById('otp').value;

    if (enteredOTP) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "verify_otp.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = xhr.responseText;
                if (response === 'Success') {
                    window.location.href = "../payment_invoice.php"; // Redirect to success page
                } else {
                    displayError("Incorrect OTP. Please try again.");
                }
            } else {
                alert("Failed to verify OTP. Please try again.");
            }
        };

        xhr.send("otp=" + enteredOTP);
    } else {
        displayError("Please enter the OTP.");
    }
}

function displayError(message) {
    let errorDiv = document.getElementById('errorDiv');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'errorDiv';
        errorDiv.className = 'text-red-500 text-sm mt-2';
        document.getElementById('passwordSection').appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}
</script>

</body>
</html>
