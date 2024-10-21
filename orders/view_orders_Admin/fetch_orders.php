<?php
$conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

if (!$conn) {
    die("No DB connection");
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$orderStatus = isset($_GET['status']) ? $_GET['status'] : ''; // Add a status filter
$allList = isset($_GET['allList']) ? $_GET['allList'] : '';

// Calculate the date for 14 days ago
$date14DaysAgo = date('Y-m-d', strtotime('-14 days'));

if (is_numeric($query)) {
    // Search by Order ID
    $sql = "SELECT * FROM orders WHERE orderID = ? ";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $query);
} elseif (!empty($orderStatus)) {
    // Filter by Order Status
    $sql = "SELECT * FROM orders WHERE orderStatus = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderStatus);
} elseif ($allList === 'true') {
    // Display all orders from the beginning
    $sql = "SELECT * FROM orders";
    $stmt = $conn->prepare($sql);
}elseif (!empty($query)) {
    // Search by Customer Name
    $sql = "SELECT * FROM orders WHERE name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $query . '%';
    $stmt->bind_param("s", $searchTerm);
}else {
    // Default: Show only orders from the past 14 days
    $sql = "SELECT * FROM orders WHERE orderDate >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date14DaysAgo);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($orders);

$stmt->close();
$conn->close();
?>
