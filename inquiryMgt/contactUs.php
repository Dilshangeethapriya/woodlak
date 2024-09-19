<?php

session_start();

include '../config/dbconnect.php';

// ---- inquiry ----
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inquiry_submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    $sql = "INSERT INTO tickets (name, phone, email, subject, ticketText, ticketStatus, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 'New', NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $phone, $email, $subject, $message);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Inquiry sent successfully!";
    } else {
        $_SESSION['error'] = "Error submitting inquiry!";
    }
    $stmt->close();
}

// -----callback requests----
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['callback_submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $time_from = $_POST['time_from'];
    $time_to = $_POST['time_to'];
    
    $sql = "INSERT INTO callback_requests (name, phone, time_from, time_to, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $phone, $time_from, $time_to);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Callback request submitted!";
    } else {
        $_SESSION['error'] = "Error submitting callback request!";
    }
    $stmt->close();
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - WOODLAK</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../resources/css/contactUs.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

  <?php include "../includes/navbar.php"; ?>

    <div class="bg bg-transparent text-white p-10 mb-20">
        <h2 class="text-center text-3xl font-semibold mt-32 mb-10">GET IN TOUCH</h2>
        <div class="flex justify-around text-center">
           
            <div class="flex flex-col items-center">
                <div class="text-teal-800 p-4 rounded-full mb-5">
                    <img class="w-14" src="../resources/images/inquiry/circle.png" alt="address">
                </div>
                <h3 class="text-lg font-semibold mb-2">ADDRESS</h3>
                <p>Piliyandala, Sri Lanka, 10300</p>
            </div>

            <div class="flex flex-col items-center">
                <div class="text-teal-800 p-4 mb-5">
                    <img class="w-14" src="../resources/images/inquiry/phone-call.png" alt="phone">
                </div>
                <h3 class="text-lg font-semibold mb-2">PHONE</h3>
                <p>077 379 3553</p>
            </div>

            <div class="flex flex-col items-center">
                <div class="text-teal-800 p-4 mb-5">
                    <img class="w-14" src="../resources/images/inquiry/email.png" alt="email">
                </div>
                <h3 class="text-lg font-semibold mb-2">EMAIL</h3>
                <p>tsamoj@gmail.com</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col p-5 bg-transparent rounded-lg max-w-6xl mx-auto">
    
    <div class="tabs flex border-b border-gray-200">
        <button class="tab-button flex-1 p-2 text-left text-gray-800 text-xl border-transparent hover:bg-gray-200 focus:outline-none" onclick="showContent('inquiry')">Inquiry</button>
        <button class="tab-button flex-1 p-2 text-left text-gray-800 text-xl border-transparent hover:bg-gray-200 focus:outline-none" onclick="showContent('callback')">Call back</button>
    </div>

        <div class="tab-container bg-translucent flex-1 md:flex-none md:w-full p-5 ">
            
            <div id="inquiry" class="tab-content hidden">
                <h2 class="text-3xl text-center font-semibold mb-10">Send Your Inquiry</h2>
    <form method="post" action="">
        <div class="flex flex-col  max-w-3xl mx-auto">
        
            <input type="text" id="name" name="name" class="flex-1 p-2  bg-transparent  border-b-2 border-green-500 " placeholder="Name"  required>
    
            <input type="text" id="phone" name="phone" class="flex-1 p-2  bg-transparent  border-b-2 border-green-500 " placeholder="Contact No" required>

            <input type="email" id="email" name="email" class="flex-1 p-2  bg-transparent  border-b-2 border-green-500 " placeholder="Email" required>
          
            <input type="text" id="subject" name="subject" class="flex-1 p-2  bg-transparent  border-b-2 border-green-500 " placeholder="Subject" required>
     
            <textarea id="message" name="message" rows="5" class="flex-1 p-2 ra  bg-transparent  border-b-2 border-green-500 " placeholder="Message" required></textarea>
  

        <div class="flex justify-center">
            <button type="submit" name="inquiry_submit" class="mt-5 p-2 mb-4 w-1/3 bg-green-500 text-white rounded-md   hover:bg-[#543310]" >Send</button>
        </div>
      </div>
    </form>

            </div>

          
            <div id="callback" class="tab-content hidden">
                <h2 class="text-3xl text-center font-semibold my-10">Request a Callback</h2>
                <form method="post" action="">
    <div class="flex flex-col max-w-3xl mx-auto">
        <input type="text" id="name" name="name" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" placeholder="Name" required>
    
        <input type="text" id="phone" name="phone" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" placeholder="Phone" required>

        <legend class="mt-4 text-xl font-bold text-gray-400">Available Time</legend>
        <div class="flex items-center mt-2">
    <div class="flex-1 mr-2">
        <label for="time_from" class="text-xl font-bold text-gray-400">From</label>
        <input type="time" id="time_from" name="time_from" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" required>
    </div>
    <div class="flex-1">
        <label for="time_to" class="text-xl font-bold text-gray-400">To</label>
        <input type="time" id="time_to" name="time_to" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" required>
    </div>
</div>


        <div class="flex justify-center">
            <button type="submit" name="callback_submit" class="mt-20 p-2 mb-4 w-1/3 bg-green-500 text-white rounded-md hover:bg-[#543310]">Submit Callback Request</button>
        </div>
    </div>
</form>

            </div>

            

        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
                 <div class="bg-green-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-4xl">
                 <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display='none';">&times;</span>
                 <?= $_SESSION['success']; ?>
                  <?php unset($_SESSION['success']); ?>
            </div>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="bg-red-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-4xl">
                    <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display='none';">&times;</span>
                    <?= $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
    <footer class="bg-[#543310] text-white mt-10">
    <div class="container mx-auto py-6 px-4 flex flex-col md:flex-row justify-between items-center">
        
        <div class="flex justify-center md:justify-start mb-4 md:mb-0">
            <a href="https://web.facebook.com/woodlak123" class="text-white mx-2 hover:text-gray-400">
                <i class="bi bi-facebook text-xl"></i>
            </a>
            <a href="#" class="text-white mx-2 hover:text-gray-400">
                <i class="bi bi-instagram text-xl"></i>
            </a>

        </div>

        
         <div class="text-center md:text-left">
            <p>&copy; 2024 WOODLAK. All rights reserved. 
                <a href="../orders/terms_and_conditions/termsAndCondition.php" class="text-white hover:text-gray-400 ml-2">Terms and Conditions</a>
            </p>
        </div>

       
        <div class="mt-4 md:mt-0">
            <a href="#" class="text-white hover:text-gray-400">
                <i class="bi bi-arrow-up-circle-fill text-2xl"></i>
            </a>
        </div>
    </div>
</footer>
<script type="text/javascript">
        window.$crisp = [];
        window.CRISP_WEBSITE_ID = "a8e437fa-4387-4062-95ab-74a78f886f17";
        (function() {
            var d = document;
            var s = d.createElement("script");
            s.src = "https://client.crisp.chat/l.js";
            s.async = 1;
            d.getElementsByTagName("head")[0].appendChild(s);
        })();
    </script>
    
    <script src="../resources/JS/contactUs.js"></script>
</body>
</html>