<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php'; // Ensure correct path to autoload.php

use Infobip\Configuration;
use Infobip\Api\SendSmsApi;
use Infobip\Model\SmsAdvancedTextualRequest;

// Set your Infobip API credentials
$apiKey = 'e76a6b926e8de2f66bf83dd884525699-a8dc54fb-45a2-4c41-9e30-b461b17b9553';
$baseUrl = 'https://nm8m8j.api.infobip.com';

$configuration = new Configuration($baseUrl, $apiKey);
$smsClient = new SendSmsApi($configuration);  // Make sure the class is found

$request = new SmsAdvancedTextualRequest();
$request->setFrom('InfoSMS')
        ->setTo(['+94741857482'])
        ->setText('Hello from Infobip!');

// Send SMS
try {
    $response = $smsClient->sendSmsMessage($request);
    print_r($response);  // This will display the response details
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
