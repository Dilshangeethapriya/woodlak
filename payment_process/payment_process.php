<?php

$api_username = "xPCUNnJwde";
$api_password = "OkDY4Sj3JT";
$public_key = "-----BEGIN PUBLIC KEY----- ... -----END PUBLIC KEY-----";
$secret_key = "12e7195f-4fc2-463a-bf5f-bc5df35b02c2";


$card_number = $_POST['card_number'];
$expiration_date = $_POST['expiration_date'];
$cvv = $_POST['cvv'];
$billing_address = $_POST['billing_address'];


openssl_public_encrypt($card_number, $encrypted_card_number, $public_key);


$data = array(
    'card_number' => base64_encode($encrypted_card_number),
    'expiration_date' => $expiration_date,
    'cvv' => $cvv,
    'billing_address' => $billing_address,
    'api_username' => $api_username,
    'api_password' => $api_password
);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://payment-gateway-url.com/process-payment");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);


if ($response === FALSE) {
    echo "Payment failed.";
} else {
    echo "Payment successful!";
}
?>
