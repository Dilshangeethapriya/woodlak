<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require('fpdf/fpdf.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "WoodLak";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paymentMethod = $_POST['paymentMethod'];

    // Fetch data based on the selected payment method
    $sql = "SELECT OrderID, customerID, name, total, paymentMethod, orderStatus FROM Orders WHERE paymentMethod = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $paymentMethod);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Title
    $pdf->Cell(190, 10, 'Orders - ' . $paymentMethod, 1, 1, 'C');
    $pdf->Ln(10);

    // Table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'OrderID', 1);
    $pdf->Cell(40, 10, 'Customer Name', 1);
    $pdf->Cell(50, 10, 'Product Name', 1);
    $pdf->Cell(30, 10, 'Total Amount', 1);
    $pdf->Cell(30, 10, 'Status', 1);
    $pdf->Ln();

    // Table data
    $pdf->SetFont('Arial', '', 12);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(30, 10, $row['OrderID'], 1);
            $pdf->Cell(40, 10, $row['customerID'], 1);
            $pdf->Cell(50, 10, $row['name'], 1);
            $pdf->Cell(30, 10, $row['total'], 1);
            $pdf->Cell(30, 10, $row['orderStatus'], 1);
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(190, 10, 'No records found', 1, 1, 'C');
    }

    $stmt->close();
    $conn->close();

    // Output the PDF
    $pdf->Output('D', 'Orders_' . $paymentMethod . '.pdf');
}
?>
