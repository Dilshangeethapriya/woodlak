<?php
session_start();


$_SESSION['paymentMethod'] = "Bank Transfer";


if (!isset($_SESSION['totalPrice'])) {
    echo "Error: Total price is not set in the session.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        
        $depositAmount = $_POST['depositAmount'];
        $accountNumber = $_POST['accountNumber'];
        $accountHolder = $_POST['accountHolder'];
        $transactionID = $_POST['transactionID'];

        
        $receiptFile = null;
        if (isset($_FILES['receiptUpload']) && $_FILES['receiptUpload']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['receiptUpload']['tmp_name'];
            $fileName = $_FILES['receiptUpload']['name'];
            $fileNameCmps = explode('.', $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            
            $allowedExts = array('jpg', 'jpeg', 'png', 'pdf');
            if (in_array($fileExtension, $allowedExts)) {
                $uploadFileDir = './uploads/';
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $receiptFile = $newFileName;
                } else {
                    throw new Exception('Failed to move uploaded file.');
                }
            } else {
                throw new Exception('Unsupported file type.');
            }
        }

        
        $_SESSION['depositAmount'] = $depositAmount;
        $_SESSION['accountNumber'] = $accountNumber;
        $_SESSION['accountHolder'] = $accountHolder;
        $_SESSION['transactionID'] = $transactionID;
        $_SESSION['receiptFile'] = $receiptFile;

        
        header("Location: payment_invoice.php");
        exit;
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recaptchaSecret = '6Lf5F0sqAAAAALHurOXVcXS8tRuExhr7VHjD2S2l';
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $responseData = json_decode($verifyResponse);

    if ($responseData->success) {
        try {
            $depositAmount = $_POST['depositAmount'];
            $accountNumber = $_POST['accountNumber'];
            $accountHolder = $_POST['accountHolder'];
            $transactionID = $_POST['transactionID'];
            
            // Your file upload and session handling logic...

            header("Location: payment_invoice.php");
            exit;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Please complete the reCAPTCHA verification.";
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transfer Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/gsap.min.js"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <style>
        .bg-neemwood {
            background-color: #a67b5b;
        }
    </style>
</head>

<body class="bg-neemwood p-2">

<?php include "../includes/navbar.php"; ?>

<script>
    AOS.init();
</script>



<div class="max-w-md mx-auto bg-white p-2 rounded-lg shadow-md mt-10" data-aos="fade-up">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Bank Transfer Instructions</h2>
        
        <p class="text-gray-700 mb-2" data-aos="fade-right">Account Number: <span class="font-semibold">8018887521</span></p>
        <p class="text-gray-700 mb-2" data-aos="fade-right">Name of the Account Holder: <span class="font-semibold">K.D.M.Perera</span></p>
        <p class="text-gray-700 mb-4" data-aos="fade-right">Branch: <span class="font-semibold">Commercial Bank - KALUTARA</span></p>

        <p class="text-red-600 font-bold mb-4" data-aos="fade-left">Please note! Only do the payment for your purchase.</p>

        <form action="" method="post" enctype="multipart/form-data">
            <input type="number" id="deposit-amount" name="depositAmount" 
                   value="<?php echo $_SESSION['totalPrice']; ?>" 
                   class="w-full mb-4 p-2 border border-gray-300 rounded" readonly required data-aos="zoom-in">

            <label for="account-number" class="block text-gray-700 mb-2" data-aos="fade-up">Your Account No:</label>
            <input type="text" id="account-number" name="accountNumber" placeholder="Your Account No"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required data-aos="zoom-in">

            <label for="account-holder" class="block text-gray-700 mb-2" data-aos="fade-up">Name of the Account Holder:</label>
            <input type="text" id="account-holder" name="accountHolder" placeholder="Name of the Account Holder"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required data-aos="zoom-in">

            <label for="transaction-id" class="block text-gray-700 mb-2" data-aos="fade-up">Transaction ID (UTR, Reference No):</label>
            <input type="text" id="transaction-id" name="transactionID" placeholder="Transaction ID"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required data-aos="zoom-in">

            <label for="receipt-upload" class="block text-gray-700 mb-2" data-aos="fade-up">Upload Transaction Receipt (jpg, jpeg, png, pdf):</label>
            <input type="file" id="receipt-upload" name="receiptUpload" accept=".jpg,.jpeg,.png,.pdf"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required data-aos="fade-up">

            <div class="g-recaptcha" data-sitekey="6Lf5F0sqAAAAAO6g3sVnOqirghLG0749p2gEnxGz"></div>

            <p class="text-red-600 font-bold mb-4" data-aos="fade-up">* Click confirm after the submission</p>

            <button type="submit" id="confirm-btn" class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600" >CONFIRM</button>
        </form>
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
    </script>


<script>
        // Initialize AOS
        AOS.init();

        // GSAP Animation for the Confirm button
        const confirmButton = document.getElementById('confirm-btn');
        confirmButton.addEventListener('click', () => {
            gsap.to("#confirm-btn", { duration: 0.5, scale: 1.1, yoyo: true, repeat: 1 });
        });
    </script>
</body>

</html>
