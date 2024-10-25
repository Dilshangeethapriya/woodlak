<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php'; // Ensure correct path to autoload.php

use Infobip\Configuration;
use Infobip\Api\SendSmsApi;
use Infobip\Model\SmsAdvancedTextualRequest;

// Set your Infobip API credentials
$apiKey = '089bb92afb31c0fbda1ccdfd52bf6968-a71016d0-68fc-4bf7-a062-439ee5739c25';
$baseUrl = 'http://nmdm8y.api.infobip.com';

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
