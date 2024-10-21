<?php
session_start();

// Enable error reporting to catch issues
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Import necessary Infobip classes
use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;

require __DIR__ . "/vendor/autoload.php"; // Correct path to autoload.php

if (isset($_POST["mobileNumber"])) {
    $phoneNumber = $_POST["mobileNumber"];
    $message = rand(100000, 999999); // Generate a 6-digit OTP

    // Store OTP in session to verify later
    $_SESSION['otp'] = $message;

    // Infobip credentials
    $apiURL = "699vy5.api.infobip.com"; // Make sure this is correct
    $apiKey = "8feeb80acc392b10926d0e650adc35a7-e92ca7f6-a55d-472b-a229-d7e38c9c1300"; // Use a valid API key

    // Create configuration object
    $configuration = new Configuration(host: $apiURL, apiKey: $apiKey);
    $api = new SmsApi(config: $configuration);

    // Create destination and message
    $destination = new SmsDestination(to: $phoneNumber);
    $theMessage = new SmsTextualMessage(
        destinations: [$destination],
        text: "Your OTP is " . $message,
        from: "InfoSMS"
    );

    // Send the request
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
