<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);


use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;

require __DIR__ . "/vendor/autoload.php"; 

if (isset($_POST["mobileNumber"])) {
    $phoneNumber = $_POST["mobileNumber"];
    $message = rand(100000, 999999);

    $_SESSION['otp'] = $message;

    $apiURL = "nmdm8y.api.infobip.com"; 
    $apiKey = "fe4fc10b0c4884d7c6f9b9d95fd0ac86-04a6b439-4da5-4fd0-b11d-471ea0bd994d"; // Use a valid API key

    $configuration = new Configuration(host: $apiURL, apiKey: $apiKey);
    $api = new SmsApi(config: $configuration);

    $destination = new SmsDestination(to: $phoneNumber);
    $theMessage = new SmsTextualMessage(
        destinations: [$destination],
        text: "Your OTP is " . $message,
        from: "InfoSMS"
    );

    $request = new SmsAdvancedTextualRequest(messages: [$theMessage]);

    try {
        $response = $api->sendSmsMessage($request);
        echo 'Success'; // Respond to AJAX
    } catch (Exception $e) {
        echo 'Failed to send SMS: ' . $e->getMessage();
    }
} else {
    echo 'Mobile number is missing!';
}
?>
