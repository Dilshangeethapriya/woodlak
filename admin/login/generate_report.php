<?php
include 'config.php';

$query = "SELECT * FROM `customer`";
$query_run = mysqli_query($conn, $query);

if (mysqli_num_rows($query_run) > 0) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="registered_users.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['ID', 'Name', 'Email', 'Contact', 'Gender', 'Postal Code', 'House No', 'Street Name', 'City']);

    while ($row = mysqli_fetch_assoc($query_run)) {
        fputcsv($output, [
            $row['customerID'],
            $row['name'],
            $row['email'],
            $row['contact'],
            $row['gender'],
            $row['postalCode'],
            $row['houseNo'],
            $row['streetName'],
            $row['city']
        ]);
    }

    fclose($output);
    exit(); 
} else {
    echo "No records found.";
}
?>
