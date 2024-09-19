<?php
session_start();

    $totalQuantity = 0;
    $totalPrice = 0.00;
    $productSequence = "";

    foreach ($_SESSION['cart'] as $cartItem) {
        $totalQuantity += $cartItem['quantity'];
        $totalPrice += $cartItem['quantity'] * $cartItem['price'];
        $productSequence .= $cartItem['productName'] . " x " . $cartItem['quantity'] . ", ";
    }

    $productSequence = rtrim($productSequence, ', ');

$_SESSION["name"] = $_POST["name"];
$_SESSION["phoneNumber"] = $_POST["phone-Num"];
$_SESSION["addressOne"] = $_POST["Address-one"];
$_SESSION["addressTwo"] = $_POST["Address-Two"];
$_SESSION["addressThree"] = $_POST["Address-three"];
$_SESSION["addressFour"] = $_POST["Address-four"];
$_SESSION["email"] = $_POST["Email"];
$_SESSION['totalQuantity'] = $totalQuantity;
$_SESSION['totalPrice'] = $totalPrice;
$_SESSION['productSequence'] = $productSequence;

header("Location: ../../payment_process/paymentmethod.php");
exit();
?>
