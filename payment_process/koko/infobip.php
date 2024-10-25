<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php'; // Ensure correct path to autoload.php

use Infobip\Configuration;
use Infobip\Api\SendSmsApi;
use Infobip\Model\SmsAdvancedTextualRequest;


$apiKey = 'fe4fc10b0c4884d7c6f9b9d95fd0ac86-04a6b439-4da5-4fd0-b11d-471ea0bd994d';
$baseUrl = 'https://nmdm8y.api.infobip.com';

$configuration = new Configuration($baseUrl, $apiKey);
$smsClient = new SendSmsApi($configuration); 

$request = new SmsAdvancedTextualRequest();
$request->setFrom('InfoSMS')
        ->setTo(['+94741857482'])
        ->setText('Hello from Infobip!');


try {
    $response = $smsClient->sendSmsMessage($request);
    print_r($response); 
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
