<?php

session_start();

include '../config/dbconnect.php';



$customerID = null;
$customerName = null;
$customerEmail = null;
$customerContact = null;

if (isset($_SESSION['user_name'])) {
    $customerID = $_SESSION['user_id'];
    $customerName = $_SESSION['user_name'];

    // Fetch customer data
    $CustomerDataSql = "SELECT * FROM `customer` WHERE customerID = '$customerID'";
    $customerData = mysqli_query($conn, $CustomerDataSql) or die('query failed');
    if (mysqli_num_rows($customerData) > 0) {
        $customerInfo = mysqli_fetch_assoc($customerData);
        $customerEmail = $customerInfo['email']; // Corrected column name
        $customerContact = $customerInfo['contact'];
    }
}

// ---- inquiry ----
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inquiry_submit'])) {


    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    $sql = "INSERT INTO tickets (customerID,name, phone, email, subject, ticketText, ticketStatus, created_at, updated_at)
            VALUES (?,?, ?, ?, ?, ?, 'New', NOW(), NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss",$customerID, $name, $phone, $email, $subject, $message);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../resources/css/contactUs.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

  <?php include "../includes/navbar.php"; ?>

    <div class="bg bg-transparent text-white p-10 mb-20">
        <h2 class="text-center text-3xl font-semibold mt-32 mb-10">GET IN TOUCH  <? echo "$customerName" ?></h2>
        <div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap justify-center gap-6">
        <!-- Address Card -->
        <div class="flex flex-col items-center bg-white rounded-lg shadow-md p-6 w-full sm:w-64">
            <div class="mb-4">
                <img src="../resources/images/inquiry/circle.png" alt="address" class="w-12 h-12 object-contain">
            </div>
            <h3 class="uppercase font-bold text-sm mb-2">ADDRESS</h3>
            <p class="text-gray-600 text-sm text-center">
                Piliyandala, Sri Lanka, 10300
            </p>
        </div>

        <!-- Phone Card -->
        <div class="flex flex-col items-center bg-white rounded-lg shadow-md p-6 w-full sm:w-64">
            <div class="mb-4">
                <img src="../resources/images/inquiry/phone-call.png" alt="phone" class="w-12 h-12 object-contain">
            </div>
            <h3 class="uppercase font-bold text-sm mb-2">PHONE</h3>
            <p class="text-gray-600 text-sm text-center">
                077 379 3553
            </p>
        </div>

        <!-- Email Card -->
        <div class="flex flex-col items-center bg-white rounded-lg shadow-md p-6 w-full sm:w-64">
            <div class="mb-4">
                <img src="../resources/images/inquiry/email.png" alt="email" class="w-12 h-12 object-contain">
            </div>
            <h3 class="uppercase font-bold text-sm mb-2">EMAIL</h3>
            <p class="text-gray-600 text-sm text-center">
                tsamoj@gmail.com
            </p>
        </div>
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
        
            <input type="text" id="name" name="name" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" 
                placeholder="Name" value="<?= isset($customerName) ? htmlspecialchars($customerName) : '' ?>" required>

            <input type="text" id="phone" name="phone" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" 
                placeholder="Contact No" value="<?= isset($customerContact) ? htmlspecialchars($customerContact) : '' ?>" required>

            <input type="email" id="email" name="email" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" 
                placeholder="Email" value="<?= isset($customerEmail) ? htmlspecialchars($customerEmail) : '' ?>" required>
          
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
    <input type="text" id="name" name="name" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" 
                placeholder="Name" value="<?= isset($customerName) ? htmlspecialchars($customerName) : '' ?>" required>
    
            <input type="text" id="phone" name="phone" class="flex-1 p-2 bg-transparent border-b-2 border-green-500" 
                placeholder="Phone" value="<?= isset($customerContact) ? htmlspecialchars($customerContact) : '' ?>" required>

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

            <?php if (isset($customerID)): ?>
                   <div class="mx-auto mb-20 flex justify-center">
                       <a class="text-lg text-white font-bold hover:underline bg-transparent rounded-md px-2 py-1" href="inquiryHistory.php" title="View detailed inquiry analytics reports">
                           <i class="fa-solid fa-clock-rotate-left"></i> My Inquiry History
                       </a>
                   </div>
            <?php endif; ?>

            
   <?php include '../includes/footer.php'; ?>
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