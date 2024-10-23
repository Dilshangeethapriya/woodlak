<?php




session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
if (isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];
    echo "Order ID received: " . $orderID; // Add this line to check if orderID is passed
} else {
    echo "Order ID not received.";
}

if (isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];

    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Update the order status to "Completed"
    $sql = "UPDATE orders SET orderStatus = 'Completed' WHERE orderID = ? and orderStatus = 'Delivered' ";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $orderID);

    if ($stmt->execute()) {
        echo "Order status updated to Completed.";
    } else {
        // Output the error for debugging purposes
        echo "Error updating order status: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
