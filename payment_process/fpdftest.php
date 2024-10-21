<?php
require('fpdf/fpdf.php');

// Create instance of FPDF class
$pdf = new FPDF();
$pdf->AddPage();

// Set font for the document
$pdf->SetFont('Arial', 'B', 16);

// Add a cell with some text
$pdf->Cell(40, 10, 'Hello World!');

// Output the PDF to the browser
$pdf->Output();
?>
