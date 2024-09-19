<?php

include('../../config/dbconnect.php');


require '../../includes/phpmailer-master/src/PHPMailer.php';
require '../../includes/phpmailer-master/src/SMTP.php';
require '../../includes/phpmailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketID = $_POST['ticketID'];
    $replyText = $_POST['reply'];

  
    if (empty($ticketID) || empty($replyText)) {
        $error = 'Invalid input or missing reply.';
        header('Location: view_inquiry.php?ticketID=' . $ticketID . '&error=' . urlencode($error));
        exit();
    }

 
    $sql = "INSERT INTO ticket_replies (ticketID, replyText, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";

    if ($stmt = $conn->prepare($sql)) {
     
        $stmt->bind_param("is", $ticketID, $replyText);

        if ($stmt->execute()) {
         
            if (sendReplyEmailToCustomer($ticketID, $replyText)) {
                $success = 'Reply sent successfully !';
                header('Location: viewInquiry.php?id=' . $ticketID . '&success=' . urlencode($success));
            } else {
                $error = 'Reply sent, but failed to send email notification.';
                header('Location: viewInquiry.php?id=' . $ticketID . '&error=' . urlencode($error));
            }
            exit();
        } else {
            $error = 'Failed to send reply.';
            header('Location: viewInquiry.php?id=' . $ticketID . '&error=' . urlencode($error));
            exit();
        }

        $stmt->close();
    } else {
        $error = 'Database error.';
        header('Location: viewInquiry.php?id=' . $ticketID . '&error=' . urlencode($error));
        exit();
    }
}

$conn->close();


function sendReplyEmailToCustomer($ticketID, $replyText) {
    global $conn;

    $query = "SELECT email, name FROM tickets WHERE ticketID = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $ticketID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
            $to = $customer['email'];  
            $name = $customer['name']; 

            // PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                // SMTP acc config
                $mail->isSMTP();
                $mail->Host = 'smtp.mailtrap.io';  
                $mail->SMTPAuth = true;
                $mail->Username = '62455afb41371f';  
                $mail->Password = '94565583a9f76f'; 
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // creating email content
                $mail->setFrom('customersupport@woodlak.com', 'WOODLAK');
                $mail->addAddress($to, $name);  
                $mail->isHTML(true);  

                $mail->Subject = "Reply to Your Inquiry (Ticket ID: $ticketID)";
                $mail->Body = "<p>Dear " . htmlspecialchars($name) . ",</p> 
                               <p>" . nl2br(htmlspecialchars($replyText)) . "</p>
                               <p>Thank you for reaching out to us!</p>";

               
                $mail->send();
                return true; 
            } catch (Exception $e) {
                error_log("PHPMailer Error: " . $mail->ErrorInfo);
                return false;
            }
        } else {
            $error = 'Customer not found.';
            header('Location: viewInquiry.php?id=' . $ticketID . '&error=' . urlencode($error));
            return false;
        }

        $stmt->close();
    }

    return false;
}
?>
