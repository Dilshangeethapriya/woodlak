<?php
session_start();

    $totalQuantity = 0;
    $totalPrice = 0.00;
    $productSequence = "";

    //Array to store the Id,name,qty
    $stockDetails = [];

    foreach ($_SESSION['cart'] as $cartItem) {
        $totalQuantity += $cartItem['quantity'];
        $totalPrice += $cartItem['quantity'] * $cartItem['price'];
        $productSequence .= $cartItem['productName'] . " x " . $cartItem['quantity'] . ", ";

        //store the Id,name,qty

        $stockDetails[] = [
            'productID' => $cartItem['productID'],
            'productName' => $cartItem['productName'],
            'quantity' => $cartItem['quantity']
        ];
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

//stock details
$_SESSION['stockDetails'] = $stockDetails;

header("Location: ../../payment_process/paymentmethod.php");
exit();
?>
