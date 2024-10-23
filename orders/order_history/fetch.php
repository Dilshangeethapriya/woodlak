<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$customerID = $_SESSION['user_id'];
$status = isset($_GET['status']) ? $_GET['status'] : '';

$conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($status == '') {
    $sql = "SELECT orderID, orderDate, paymentMethod, total, orderStatus FROM orders_view WHERE customerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerID);
} else {
    $sql = "SELECT orderID, orderDate, paymentMethod, total, orderStatus FROM orders_view WHERE customerID = ? AND orderStatus = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $customerID, $status);
}

$stmt->execute();
$result = $stmt->get_result();

$output = '';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>";
        $output .= "<td>{$row['orderID']}</td>";
        $output .= "<td>{$row['orderDate']}</td>";
        $output .= "<td>{$row['paymentMethod']}</td>";
        $output .= "<td>Rs.{$row['total']}</td>";
        $output .= "<td >
        
        <div>{$row['orderStatus']}</div>
    </td>";
    $output .= "<td style='position: relative;'>
    <button class='delete-btn' data-order-id='{$row['orderID']}'><i class='fas fa-times'></i></button>
    <button class='confirm-order-btn' data-order-id='{$row['orderID']}'>Confirm Your Order</button>
</td>";
    
    

        $output .= "</tr>";
    }
} else {
    $output .= "<tr><td colspan='6'>No orders found</td></tr>";
}

echo $output;

$stmt->close();
$conn->close();
?>
