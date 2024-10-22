<?php
require('libs/fpdf/fpdf.php');
include 'dbcon.php';

if (isset($_GET['type'])) {
    $type = isset($_GET['type']) ? $_GET['type'] : 'balance';
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

    if ($type === 'in') {

        $fetchSql = "SELECT * FROM log_stockin WHERE date >= '$start_date' AND date <= '$end_date'";
        
    } elseif ($type === 'out') {

        $fetchSql = "SELECT * FROM log_stockout WHERE date >= '$start_date' AND date <= '$end_date'";

    } else {

        $fetchSql = "SELECT * FROM product";  
    }

    if ($fetchSql != '') {
        $result = mysqli_query($conn, $fetchSql);

        if ($result === FALSE) {
            die("Error fetching data: " . mysqli_error($conn));
        }
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->Image('Logo.png', 95, 10, 20, 20);
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 20, 'WOODLAK', 0, 1, 'C');

        
        $pdf->SetFont('Arial', 'B', 14);
        if ($type == 'in') {
            $pdf->Cell(0, 10, 'Stock In Report', 0, 1, 'C');
        } elseif ($type == 'out') {
            $pdf->Cell(0, 10, 'Stock Out Report', 0, 1, 'C');
        } elseif ($type == 'balance') {
            $pdf->Cell(0, 10, 'Stock Balance Report', 0, 1, 'C');
        }


        date_default_timezone_set('Asia/Colombo');
        $currentDate = date('d/m/Y');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'As At ' . $currentDate, 0, 1, 'C');

        
        $pdf->SetFont('Arial', 'B', 12);
        if ($type == 'in' || $type == 'out') {
            $pdf->SetX(20);
            $pdf->Cell(40, 10, 'Date', 1, 0, 'C');
            $pdf->Cell(40, 10, 'Product ID', 1, 0, 'C');
            $pdf->Cell(60, 10, 'Product Name', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
            $pdf->Ln();
        } elseif ($type == 'balance') {
            $pdf->SetX(25);
            $pdf->Cell(40, 10, 'Product ID', 1, 0, 'C');
            $pdf->Cell(80, 10, 'Product Name', 1, 0, 'C');
            $pdf->Cell(40, 10, 'Stock Balance', 1, 0, 'C');
            $pdf->Ln();
        }


        $pdf->SetFont('Arial', '', 12);
        while ($row = mysqli_fetch_assoc($result)) {
            if ($type == 'in' || $type == 'out') {
                $pdf->SetX(20);
                $pdf->Cell(40, 10, $row['date'], 1);
                $pdf->Cell(40, 10, $row['productId'], 1);
                $pdf->Cell(60, 10, $row['productName'], 1);
                $pdf->Cell(30, 10, $row['qty'], 1);
                $pdf->Ln();
            } elseif ($type == 'balance') {
                $pdf->SetX(25);
                $pdf->Cell(40, 10, $row['productID'], 1);
                $pdf->Cell(80, 10, $row['productName'], 1);
                $pdf->Cell(40, 10, $row['stockLevel'], 1);
                $pdf->Ln();
            }
        }

        if($type=='in'){
            $pdf->Output('D', 'Stock In Report.pdf');
            exit();
        }else if($type=='out'){
            $pdf->Output('D', 'Stock Out Report.pdf');
            exit();
        }else if($type=='balance'){
            $pdf->Output('D', 'Stock Balance Report.pdf');
            exit();
        }
        
    }
}
?>
