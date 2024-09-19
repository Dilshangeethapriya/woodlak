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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transfer Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .bg-neemwood {
            background-color: #a67b5b;
        }
    </style>
</head>

<body class="bg-neemwood p-2">

<?php include "../includes/navbar.php"; ?>

    <div class="max-w-md mx-auto bg-white p-2 rounded-lg shadow-md mt-10">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Bank Transfer Instructions</h2>
        
        <p class="text-gray-700 mb-2">Account Number: <span class="font-semibold">8018887521</span></p>
        <p class="text-gray-700 mb-2">Name of the Account Holder: <span class="font-semibold">K.D.M.Perera</span></p>
        <p class="text-gray-700 mb-4">Branch: <span class="font-semibold">Commercial Bank - KALUTARA</span></p>

        <p class="text-red-600 font-bold mb-4">Please note! Only do the payment for your purchase.</p>

        <form action="" method="post" enctype="multipart/form-data">
            
            <input type="number" id="deposit-amount" name="depositAmount" 
                   value="<?php echo $_SESSION['totalPrice']; ?>" 
                   class="w-full mb-4 p-2 border border-gray-300 rounded" readonly required>

            <label for="account-number" class="block text-gray-700 mb-2">Your Account No:</label>
            <input type="text" id="account-number" name="accountNumber" placeholder="Your Account No"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required>

            <label for="account-holder" class="block text-gray-700 mb-2">Name of the Account Holder:</label>
            <input type="text" id="account-holder" name="accountHolder" placeholder="Name of the Account Holder"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required>

            <label for="transaction-id" class="block text-gray-700 mb-2">Transaction ID (UTR, Reference No):</label>
            <input type="text" id="transaction-id" name="transactionID" placeholder="Transaction ID"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required>

            <label for="receipt-upload" class="block text-gray-700 mb-2">Upload Transaction Receipt (jpg, jpeg, png, pdf):</label>
            <input type="file" id="receipt-upload" name="receiptUpload" accept=".jpg,.jpeg,.png,.pdf"
                class="w-full mb-4 p-2 border border-gray-300 rounded" required>

            <p class="text-red-600 font-bold mb-4">* Click confirm after the submission</p>

            <button type="submit" class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">CONFIRM</button>
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
</body>

</html>
