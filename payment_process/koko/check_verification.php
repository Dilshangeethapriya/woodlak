<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the session variable is set
    if (isset($_SESSION['verificationCode'])) {
        $enteredCode = $_POST['enteredCode'];

        // Check if the entered code matches the one in the session
        if ($enteredCode == $_SESSION['verificationCode']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid verification code.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No verification code found.']);
    }
}
?>
