<?php

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");


if (!$conn) {
    die("No DB connection");
}

// Get the orderID and new status from POST request
if (isset($_POST['orderID']) && isset($_POST['status'])) {
    $orderID = intval($_POST['orderID']);
    $status = $_POST['status'];

    // Validate the status to allow only specific values

   

   $orderID = intval($orderID);
    $status = mysqli_real_escape_string($conn, $status);

    // Update the order status
    $sql = "UPDATE orders SET orderStatus = ? WHERE orderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $orderID);
    $stmt->execute();


    if ($stmt->affected_rows > 0) {
        echo "Order status updated successfully.";
    } else {
        echo "Failed to update order status.";
    }

    $stmt->close();
} else {
    echo "Missing orderID or status.";
}






// Close the database connection
$conn->close();
?>
