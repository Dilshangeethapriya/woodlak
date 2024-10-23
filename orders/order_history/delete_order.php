<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if (isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];

    $conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // SQL to delete the order by orderID
    $sql = "DELETE FROM orders_view WHERE orderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderID);
    
    if ($stmt->execute()) {
        echo "Order deleted successfully";
    } else {
        echo "Error deleting order: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No orderID received.";
}
?>
