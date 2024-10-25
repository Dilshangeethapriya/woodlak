<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);


$receiptNumber = strtoupper(uniqid('REC-'));

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "woodlak";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $totalAmount = isset($_SESSION['totalPrice']) ? $_SESSION['totalPrice'] : 0.00;
    $orderStatus = "Completed";
    $combPurchased = isset($_SESSION['productSequence']) ? $_SESSION['productSequence'] : 'No products purchased';
    $quantity = isset($_SESSION['totalQuantity']) ? $_SESSION['totalQuantity'] : 0;

    
    if (!isset($_SESSION['name'], $_SESSION['phoneNumber'], $_SESSION['addressOne'], $_SESSION['addressTwo'], $_SESSION['addressThree'], $_SESSION['addressFour'], $_SESSION['email'])) {
        throw new Exception("Some required session variables are missing.");
    }

  
    $stmt = $conn->prepare("INSERT INTO Orders (customerID, total, paymentMethod, orderStatus, name, phoneNumber, houseNo, streetName, city, postalCode, email, combPurchased, quantity) 
    VALUES (:customerID, :total, :paymentMethod, :orderStatus, :name, :phoneNumber, :houseNo, :streetName, :city, :postalCode, :email, :combPurchased, :quantity)");

    $stmt->bindParam(':customerID', $_SESSION['user_id']);
    $stmt->bindParam(':total', $totalAmount);
    $stmt->bindParam(':paymentMethod', $_SESSION['paymentMethod']);
    $stmt->bindParam(':orderStatus', $orderStatus);
    $stmt->bindParam(':name', $_SESSION['name']);
    $stmt->bindParam(':phoneNumber', $_SESSION['phoneNumber']);
    $stmt->bindParam(':houseNo', $_SESSION['addressOne']);
    $stmt->bindParam(':streetName', $_SESSION['addressTwo']);
    $stmt->bindParam(':city', $_SESSION['addressThree']);
    $stmt->bindParam(':postalCode', $_SESSION['addressFour']);
    $stmt->bindParam(':email', $_SESSION['email']);
    $stmt->bindParam(':combPurchased', $combPurchased);
    $stmt->bindParam(':quantity', $quantity);

    
    $stmt->execute();

    $orderID = $conn->lastInsertId();

    if ($_SESSION['paymentMethod'] == 'Bank Transfer') {
        $depositAmount = $_SESSION['depositAmount'];
        $accountNumber = $_SESSION['accountNumber'];
        $accountHolder = $_SESSION['accountHolder'];
        $transactionID = $_SESSION['transactionID'];
        $receiptFile = $_SESSION['receiptFile'];

    
        $stmt = $conn->prepare("INSERT INTO BankTransfers (depositAmount, accountNumber, accountHolder, transactionID, receiptFile, OrderID) 
                                VALUES (:depositAmount, :accountNumber, :accountHolder, :transactionID, :receiptFile, :orderID)");
        
        $stmt->bindParam(':depositAmount', $depositAmount);
        $stmt->bindParam(':accountNumber', $accountNumber);
        $stmt->bindParam(':accountHolder', $accountHolder);
        $stmt->bindParam(':transactionID', $transactionID);
        $stmt->bindParam(':receiptFile', $receiptFile);
        $stmt->bindParam(':orderID', $orderID);

        $stmt->execute();
    }

    //To Mange Stocks

    if(isset($_SESSION['stockDetails'])){
        foreach ($_SESSION['stockDetails'] as $product) {
            $productID = $product['productID'];
            $productName = $product['productName'];
            $quantity = $product['quantity'];

            //insert into the log_stockOut Table

            $stockOutSql = "INSERT INTO log_stockOut (date, productID, productName, qty) VALUES (NOW(), :productID, :productName, :qty)";

            $stmt = $conn->prepare($stockOutSql);
            $stmt->bindParam(':productID', $productID);
            $stmt->bindParam(':productName', $productName);
            $stmt->bindParam(':qty', $quantity);
            $stmt->execute();
//Update the current stock

            $updateStockSql = "UPDATE product SET stockLevel = stockLevel - :quantity WHERE productID = :productID";

            $updateStmt = $conn->prepare($updateStockSql);
            $updateStmt->bindParam(':quantity', $quantity);
            $updateStmt->bindParam(':productID', $productID);
            $updateStmt->execute();
        }
    }else{
        echo "No products in the session.";
    }

    $conn = null;
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}

if (isset($orderID)) {
    $receiptMessage = "<h2>Payment Invoice</h2>
    <p>Thank you for your payment.</p>
    <p>Order ID: " . htmlspecialchars($orderID) . "</p>
    <p>Receipt Number: " . htmlspecialchars($receiptNumber) . "</p>
    <p>Total Amount: $" . htmlspecialchars($totalAmount) . "</p>";
} else {
    $receiptMessage = "<p>Error processing the order. Please try again later.</p>";
}



if (isset($_SESSION['email'])) {
    $customerEmail = $_SESSION['email'];  // Fetch the email from the session
} else {
    echo "No email found in session!";
    exit;
}

$invoiceDetails = "
    <h4>Invoice Details:</h4>
    <table style='width: 100%; border-collapse: collapse;'>
        <tr>
            <th style='border: 1px solid #ddd; padding: 8px;'>Description</th>
            <th style='border: 1px solid #ddd; padding: 8px;'>Details</th>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Receipt Number:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($receiptNumber) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Order ID:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($orderID) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Customer Name:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($_SESSION['name']) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Phone Number:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($_SESSION['phoneNumber']) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Address:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($_SESSION['addressOne']) .", ". htmlspecialchars($_SESSION['addressTwo']) .", ". htmlspecialchars($_SESSION['addressThree']) .", ". htmlspecialchars($_SESSION['addressFour']) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Email:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($_SESSION['email']) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Payment Method:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($_SESSION['paymentMethod']) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Total Amount:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>$". htmlspecialchars($_SESSION['totalPrice']) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Quantity:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($_SESSION['totalQuantity']) ."</td>
        </tr>
        <tr>
            <td style='border: 1px solid #ddd; padding: 8px;'>Combs Purchased:</td>
            <td style='border: 1px solid #ddd; padding: 8px;'>". htmlspecialchars($combPurchased) ."</td>
        </tr>
    </table>
";


$logoPath = 'Logo.png';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'microcryptosoft2022@gmail.com';  // Your Gmail address
    $mail->Password = 'mmewnrevrbgzeqcp';              // Your Gmail app-specific password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;   // Use SSL
    $mail->Port = 465;

    // Sender and recipient
    $mail->setFrom('microcryptosoft2022@gmail.com', 'Woodlak');
    $mail->addAddress($customerEmail);  // Customer's email from the session

    // Add logo as an embedded image
    $mail->AddEmbeddedImage($logoPath, 'woodlak_logo');

    // Email content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = 'Your Payment Invoice';
    
    // HTML content of the email, including the logo and invoice details
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; }
            .logo { text-align: center; }
        </style>
    </head>
    <body>
        <div class='invoice-box'>
            <div class='logo'>
                <img src='cid:woodlak_logo' alt='Woodlak Logo' style='width: 100px; height: auto;'>
            </div>
            <h2>Thank you for your payment!</h2>
            <p>Dear customer,</p>
            <p>We have received your payment successfully. Below are your invoice details:</p>
            <div>{$invoiceDetails}</div>
            <br>
            <p>Best regards,</p>
            <p>Woodlak Team</p>
        </div>
    </body>
    </html>
";

    
    $mail->AltBody = 'WOODLAK PURCHASE INVOICE';

    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Woodlak Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20;
            background: url('../resources/images/bg4.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            font-family: sans-serif;
        }
        .receipt-container {
            background-color: #ffffff;
            padding: 20px;
            margin: 0 auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333333;
        }
        .receipt-details, .payment-details, .company-details {
            margin-bottom: 20px;
        }
        .receipt-details p, .payment-details p, .company-details p {
            margin: 5px 0;
            color: #555555;
        }
        .receipt-total {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            margin-bottom: 30px;
        }
        .footer {
            text-align: center;
            color: #777777;
            font-size: 12px;
        }
        .btn-download {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #28a745;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .btn-download:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="receipt-container mt-14">
    <div class="receipt-header">
       <center> <img src="pictures/logo.png" alt="Company Logo"> </center>
        <h1>Official Payment Receipt</h1>
    </div>

    <div class="company-details">
        <h4><strong>Tracking ID: <?php echo htmlspecialchars($orderID); ?></strong></h4>
        <p><strong>Company Name:</strong> Woodlak</p>
        <p><strong>Company Address:</strong> 1234 Main Street, City, Country</p>
        <p><strong>Email:</strong> woodlak@gmail.com</p>
        <p><strong>Phone:</strong> +94 75 428 4679</p>
    </div>

    <div class="receipt-details">
        <p><strong>Receipt Number:</strong> <?php echo htmlspecialchars($receiptNumber); ?></p>
        <p><strong>Date:</strong> <?php echo date('F j, Y'); ?></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($_SESSION['phoneNumber']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['addressOne']) . ", " . htmlspecialchars($_SESSION['addressTwo']) . ", " . htmlspecialchars($_SESSION['addressThree']) . ", " . htmlspecialchars($_SESSION['addressFour']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($_SESSION['paymentMethod']); ?></p>
        <?php if ($_SESSION['paymentMethod'] == 'koko') { ?>
            <p><strong>Card Number:</strong> **** **** **** 4242</p>
        <?php } ?>
    </div>

    <div class="receipt-total">
        <p>Total Amount: <?php echo htmlspecialchars($_SESSION['totalPrice']); ?></p>
    </div>

    <a href="#" class="btn-download" onclick="downloadReceipt()">Download Receipt</a>
</div>

<div class="footer">
    <p>Thank you for your purchase!</p>
    <p>If you have any questions, please contact our support team at woodlak@gmail.com</p>
</div>

<?php include "../includes/footer.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    function downloadReceipt() {
        const receipt = document.querySelector('.receipt-container');
        const opt = {
            margin:       0.5,
            filename:     '<?php echo htmlspecialchars($receiptNumber); ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().from(receipt).set(opt).save();
    }
</script>

</body>
</html>
