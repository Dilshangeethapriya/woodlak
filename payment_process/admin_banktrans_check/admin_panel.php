<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "WoodLak";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $transferID = intval($_GET['id']);
    $action = $_GET['action'];

    $sql = "SELECT OrderID FROM BankTransfers WHERE transferID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transferID);
    $stmt->execute();
    $stmt->bind_result($orderID);
    $stmt->fetch();
    $stmt->close();

    if ($action == 'delete') {
        if ($orderID) {
            $sql = "UPDATE Orders SET orderStatus = 'cancelled' WHERE orderID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $orderID);
            $stmt->execute();
            $stmt->close();
        }

        $sql = "DELETE FROM BankTransfers WHERE transferID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $transferID);

        if ($stmt->execute()) {
            echo "Transfer deleted successfully";
        } else {
            echo "Error deleting transfer: " . $conn->error;
        }

        $stmt->close();
    } elseif ($action == 'confirm') {
        $sql = "UPDATE BankTransfers SET checkStatus = 'Confirmed' WHERE transferID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $transferID);
        $stmt->execute();
        $stmt->close();

        if ($orderID) {
            $sql = "UPDATE Orders SET orderStatus = 'Confirmed' WHERE orderID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $orderID);
            $stmt->execute();
            $stmt->close();
        }

        echo "Transfer confirmed successfully";
    }

    $conn->close();

    header("Location: admin_panel.php");
    exit();
}

$sql = "SELECT transferID, depositAmount, accountNumber, accountHolder, transactionID, receiptFile, OrderID, checkStatus FROM BankTransfers";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Bank Transfers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #A67B5B;
        }
        h1 {
            text-align: center;
            color: #fff;
            padding: 20px 0;
            animation: fadeInDown 1s ease-in-out;
        }
        table {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #f4f4f4;
            animation: fadeInUp 1s ease-in-out;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #b87333;
        }
        th {
            background-color: #8C4D29;
            color: #fff;
        }
        td {
            background-color: #D9B08C; 
            color: #333;
        }
        td a {
            text-decoration: none;
            color: #0066cc;
            margin-right: 10px;
        }
        td a:hover {
            text-decoration: underline;
        }
        tr:hover {
            background-color: #C9A068;
            transition: background-color 0.3s ease;
        }
        .fadeInUp {
            animation: fadeInUp 1s ease-in-out;
        }
        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @media (max-width: 768px) {
            table, th, td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="bg-[#543310] h-20 fixed w-full top-0 mt-0">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
        <div class="flex items-center gap-[1vw]">
            <img class="w-16" src="../Logo.png" alt="Logo">
            <h1 class="text-xl text-white font-sans"><b>WOODLAK</b></h1>
            <p class="text-xl text-white font-sans">Admin</p>
        </div>
        <div class="lg:static absolute bg-[#543310] lg:min-h-fit min-h-[39vh] left-0 top-[9%] lg:w-auto w-full flex items-center px-5 justify-center lg:justify-start text-center lg:text-right xl:contents hidden lg:flex" id="content">
            <ul class="flex lg:flex-row flex-col lg:gap-[4vw] gap-8">
                <li><a class="text-white hover:text-[#D0B8A8]" href="../../admin">Dashboard</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="../../product/product_detail.php">Products</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="../../orders/view_orders_Admin/OrderList.php">Orders</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="../../admin/inquiry">Inquiries</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="">Bank Transfers</a></li>
                <li><a class="text-white hover:text-[#D0B8A8]" href="../../UserProfile/RegisteredUsers.php">Users</a></li>
            </ul>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]">Logout</button> 
            <button onclick="responsive()"><i class="bi bi-list text-4xl lg:hidden text-white"></i></button>
        </div>
    </nav>
</header>

<h1 class="mt-32 text-4xl" data-aos="fade-in">Bank Transfers Management</h1>

<table data-aos="fade-up">
    <tr>
        <th>Transfer ID</th>
        <th>Deposit Amount</th>
        <th>Account Number</th>
        <th>Account Holder</th>
        <th>Transaction ID</th>
        <th>Receipt File</th>
        <th>Order ID</th>
        <th>Check Status</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row["transferID"]; ?></td>
                <td><?php echo $row["depositAmount"]; ?></td>
                <td><?php echo $row["accountNumber"]; ?></td>
                <td><?php echo $row["accountHolder"]; ?></td>
                <td><?php echo $row["transactionID"]; ?></td>
                <td><a href="../uploads/<?php echo $row["receiptFile"]; ?>" target="_blank">View File</a></td>
                <td><?php echo $row["OrderID"]; ?></td>
                <td><?php echo $row["checkStatus"]; ?></td>
                <td>
                    <?php if ($row["checkStatus"] !== 'Confirmed'): ?>
                        <a href="admin_panel.php?id=<?php echo $row['transferID']; ?>&action=confirm" onclick="return confirm('Are you sure you want to confirm this transfer?');" class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-700 transition ease-in-out duration-300">Confirm</a>
                        <br><br>
                        <a href="admin_panel.php?id=<?php echo $row['transferID']; ?>&action=delete" onclick="return confirm('Are you sure you want to delete this transfer?');" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-700 transition ease-in-out duration-300">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9">No records found</td></tr>
    <?php endif; ?>
</table>


<form action="download_pdf.php" method="POST">
    <div class="flex justify-center mt-8">
        <label for="paymentMethod" class="mr-4 text-white">Select Payment Method:</label>
        <select name="paymentMethod" id="paymentMethod" class="bg-white text-black px-4 py-2 rounded">
            <option value="Cash On Delivery">Cash On Delivery</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Koko">Koko</option>
        </select>
        <button type="submit" class="ml-4 bg-blue-500 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition ease-in-out duration-300">Download PDF</button>
    </div>
</form>


<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>


<?php
$conn->close();
?>
