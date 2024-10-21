<?php
require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

if (!$conn) {
    die("No DB connection");
}

if (isset($_POST['orderID'])) {
    $orderID = intval($_POST['orderID']);

    $sql = "SELECT email FROM orders WHERE orderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $to = $order['email'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP(); 
            $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
            $mail->SMTPAuth = true; 
            $mail->Username = 'microcryptosoft2022@gmail.com'; // SMTP username
            $mail->Password = 'mmewnrevrbgzeqcp'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587; 
            $mail->setFrom('microcryptosoft2022@gmail.com', 'Woodlak');
            $mail->addAddress($to); 


            $mail->isHTML(true);
            $mail->Subject = "Your Order #$orderID has been Delivered";
            $mail->Body    = "
            
            <p>Dear Customer, </p>
             <p>We are pleased to inform you that your order <strong>#$orderID</strong> has been successfully delivered.</p> <p>If you have any complaints regarding the delivery, please ensure to contact us within 30 days. You can reach us via WhatsApp, Facebook, or through our Woodlak website.</p>
             <p>Kindly confirm  the delivery of your product through our website. Please note that if we do not receive any complaints within the specified period, we will consider your order as completed.</p>
             <p>Please Follow our TikTok Channel</p>
             <p>https://www.tiktok.com/@woodlak</p>
             <br> </br>
             <p>Please Like our Facebook page</p>
             <p>https://www.facebook.com/woodlak123</p>
             <br> </br>
             <p>Join our Whatsapp group for updates</p>
             <p>https://chat.whatsapp.com/EVXRIKTvIKPLmqVQUTNVZE</p>
             <br> </br>
             <p>Please visit our official WoodLak website</p>
             <p></p>
             <p>Thank you for your interest in Neem Wooden Combs!</p>";

            $mail->AltBody = "Dear Customer,\n\nYour order #$orderID has been successfully delivered.\nThank you for shopping with us!";

            $mail->send();
            echo "Email sent successfully.";
        } catch (Exception $e) {
            echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Order not found.";
    }

    $stmt->close();
} else {
    echo "Missing orderID.";
}

$conn->close();
?>
