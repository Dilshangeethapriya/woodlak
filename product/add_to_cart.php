<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = $_POST['productID'];
    $productName = $_POST['productName'];
    $price = $_POST['price'];
    

    $item = [
        'productID' => $productID,
        'productName' => $productName,
        'price' => $price,
        'quantity' => 1
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }


    $found = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['productID'] == $productID) {
            $cartItem['quantity'] += 1;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $item;
    }


    header('Location: shopping_cart.php');
    exit();
}
?>
