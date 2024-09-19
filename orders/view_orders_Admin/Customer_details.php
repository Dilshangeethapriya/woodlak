<?php

$conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

if (!$conn) {
    die("No DB connection");
} 


// Get the orderID from the URL
$orderID = $_GET['orderID'];

// Fetch the customer details for the specific orderID
$sql = "SELECT * FROM orders WHERE orderID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
if (!$customer) {
    die("No customer found with orderID: " . htmlspecialchars($orderID));
}
// Close the database connection
$stmt->close();
$conn->close();
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Details</title>
    <link rel="stylesheet" type="text/css" href="CustomerDetails.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form action="">
            <h1>CUSTOMER DETAILS</h1>
            <div class="input-box">
                <p>Name: <?php echo htmlspecialchars($customer['name']); ?></p>
            </div>
            <div class="input-box">
                <p>Phone Number: <?php echo htmlspecialchars($customer['phoneNumber']); ?></p>
            </div>
            <div class="input-box">
                <p>Email: <?php echo htmlspecialchars($customer['email']); ?></p>
            </div>
            <div class="input-box">
                <p>Comb Purchased: <?php echo htmlspecialchars($customer['combPurchased']); ?></p>
            </div>
            <div class="input-box">
                <p>Amount of combs: <?php echo htmlspecialchars($customer['quantity']); ?></p>
            </div>
        </form>
    </div>

    <div class="wrapper">
        <form action="">
            <h1>ADDRESS DETAILS</h1>
            <div class="input-box">
                <p>House No: <?php echo htmlspecialchars($customer['houseNo']); ?></p>
            </div>
            <div class="input-box">
                <p>Street Name: <?php echo htmlspecialchars($customer['streetName']); ?></p>
            </div>
            <div class="input-box">
                <p>City: <?php echo htmlspecialchars($customer['city']); ?></p>
            </div>
            <div class="input-box">
                <p>Postal Code: <?php echo htmlspecialchars($customer['postalCode']); ?></p>
            </div>
        </form>
    </div>
</body>
</html>
