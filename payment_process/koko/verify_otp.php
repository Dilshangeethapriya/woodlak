<?php
session_start();

if (isset($_POST['otp'])) {
    $enteredOTP = $_POST['otp'];
    $generatedOTP = $_SESSION['otp']; // Get the OTP stored in the session

    // Check if the session variable for OTP exists
    if (isset($generatedOTP)) {
        if ($enteredOTP == $generatedOTP) {
            // OTP matches, return success
            echo 'Success';
        } else {
            // OTP doesn't match
            echo 'Incorrect';
        }
    } else {
        echo 'No OTP generated'; // If no OTP was stored in the session
    }
} else {
    echo 'OTP not provided'; // If OTP is not sent in the request
}
?>
