<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$receiptNumber = strtoupper(uniqid('REC-'));

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // your database password here
$dbname = "woodlak";

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Assign values
    $totalAmount = isset($_SESSION['totalPrice']) ? $_SESSION['totalPrice'] : 0.00;
    $orderStatus = "Completed";
    $combPurchased = isset($_SESSION['productSequence']) ? $_SESSION['productSequence'] : 'No products purchased';
    $quantity = isset($_SESSION['totalQuantity']) ? $_SESSION['totalQuantity'] : 0;

    // Check if essential session variables are set
    if (!isset($_SESSION['name'], $_SESSION['phoneNumber'], $_SESSION['addressOne'], $_SESSION['addressTwo'], $_SESSION['addressThree'], $_SESSION['addressFour'], $_SESSION['email'])) {
        throw new Exception("Some required session variables are missing.");
    }

    // Prepare SQL and bind parameters for Orders table
    $stmt = $conn->prepare("INSERT INTO Orders (total, paymentMethod, orderStatus, name, phoneNumber, houseNo, streetName, city, postalCode, email, combPurchased, quantity) 
                            VALUES (:total, :paymentMethod, :orderStatus, :name, :phoneNumber, :houseNo, :streetName, :city, :postalCode, :email, :combPurchased, :quantity)");
    
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

    // Execute the statement for Orders table
    $stmt->execute();

    // Get the latest order ID
    $orderID = $conn->lastInsertId();

    // Insert into BankTransfers table if payment method is Bank Transfer
    if ($_SESSION['paymentMethod'] == 'Bank Transfer') {
        // Retrieve bank transfer details from session
        $depositAmount = $_SESSION['depositAmount'];
        $accountNumber = $_SESSION['accountNumber'];
        $accountHolder = $_SESSION['accountHolder'];
        $transactionID = $_SESSION['transactionID'];
        $receiptFile = $_SESSION['receiptFile'];

        // Insert into BankTransfers table
        $stmt = $conn->prepare("INSERT INTO BankTransfers (depositAmount, accountNumber, accountHolder, transactionID, receiptFile, OrderID) 
                                VALUES (:depositAmount, :accountNumber, :accountHolder, :transactionID, :receiptFile, :orderID)");
        
        $stmt->bindParam(':depositAmount', $depositAmount);
        $stmt->bindParam(':accountNumber', $accountNumber);
        $stmt->bindParam(':accountHolder', $accountHolder);
        $stmt->bindParam(':transactionID', $transactionID);
        $stmt->bindParam(':receiptFile', $receiptFile);
        $stmt->bindParam(':orderID', $orderID);

        // Execute the statement for BankTransfers table
        $stmt->execute();
    }

    // Close the connection
    $conn = null;
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Unset session variables related to the order processing
$orderSessionVars = [
    'totalPrice',
    'productSequence',
    'totalQuantity',
    'paymentMethod',
    'depositAmount',
    'accountNumber',
    'accountHolder',
    'transactionID',
    'receiptFile'
];

foreach ($orderSessionVars as $var) {
    unset($_SESSION[$var]);
}

// Generate the receipt message
if (isset($orderID)) {
    $receiptMessage = "<h2>Payment Invoice</h2>
    <p>Thank you for your payment.</p>
    <p>Order ID: " . htmlspecialchars($orderID) . "</p>
    <p>Receipt Number: " . htmlspecialchars($receiptNumber) . "</p>
    <p>Total Amount: $" . htmlspecialchars($totalAmount) . "</p>";
} else {
    $receiptMessage = "<p>Error processing the order. Please try again later.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <br>
    <br>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Woodlak Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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

<div class="receipt-container">
    <div class="receipt-header">
        <img src="logo.png" alt="Company Logo">
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

    <a href="#" class="btn-download">Download PDF</a>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is an electronically generated receipt and does not require a signature.</p>
    </div>
</div>

</body>
</html>
