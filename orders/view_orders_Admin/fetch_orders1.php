<?php
$conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

if (!$conn) {
    die("No DB connection");
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$orderStatus = isset($_GET['status']) ? $_GET['status'] : ''; // Add a status filter

if (is_numeric($query)) {
    // Search by Order ID
    $sql = "SELECT * FROM orders WHERE orderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $query);
} elseif (!empty($orderStatus)) {
    // Filter by Order Status
    $sql = "SELECT * FROM orders WHERE orderStatus = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderStatus);
} else {
    // Search by Customer Name
    $sql = "SELECT * FROM orders WHERE name like ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $query . '%';
    $stmt->bind_param("s", $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($orders);

$stmt->close();
$conn->close();
?>
