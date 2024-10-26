<?php
session_start();
include '../config/dbconnect.php';
$base_url = "http://localhost/woodlak"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Inquiry History</title>

  
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../resources/css/contactUs.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

  
  <?php include "../includes/navbar.php"; ?>

 
  <div class="container mx-auto max-w-4xl px-4 py-8 flex-grow">
    <h2 class="text-center text-4xl font-bold text-[#785b3a] my-8">My Inquiry History</h2>

   
    <div class="grid grid-cols-1 gap-6">
      <?php
        $customerID = $_SESSION['user_id']; 

        $query = "SELECT * FROM tickets WHERE customerID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $inquiries = $stmt->get_result();
        $inqNo = 1;

        if ($inquiries->num_rows > 0) {
            while ($row = $inquiries->fetch_assoc()) {
               $created_at = new DateTime($row['created_at']);
               $inquiryCreatedDate = $created_at->format('F j, Y, g:i A');

               $updated_at = new DateTime($row['updated_at']);
               $inquiryUpdatedDate = $updated_at->format('F j, Y, g:i A');

              echo "<div class='inquiry-container'>";
              echo "<h3 class='text-xl font-semibold text-teal-800 text-center mb-4'>Inquiry No ".$inqNo."</h3>";

              echo "<div class='inquiry-details mb-4'>";
                echo "<p><strong class='text-gray-800'>Status:</strong> " . htmlspecialchars($row['ticketStatus']) . "</p>";
                echo "<p><strong class='text-gray-800'>Created Date:</strong> " .$inquiryCreatedDate . "</p>";
                echo "<p><strong class='text-gray-800 '>Updated On:</strong> " . $inquiryUpdatedDate . "</p>";
                echo "<div class='p-5 my-5 rounded-lg backdrop-blur-sm bg-green-200'> <h3 class='text-xl text-center mb-3'>". $row['subject']."</h3><p>" . htmlspecialchars($row['ticketText']) . "</p></div>";

              echo "</div>";

              $ticketID = $row['ticketID'];
              $sql = "SELECT * FROM ticket_replies WHERE ticketID = ?";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("i", $ticketID);
              $stmt->execute();
              $replies = $stmt->get_result();
              $replyNum = 1;
              while ($reply = $replies->fetch_assoc()) {
                echo "<div class='reply-container'>";
                echo "<p><strong class='text-green-600'>Reply [" . $replyNum . "]:</strong> " . htmlspecialchars($reply['replyText']) . "</p>";
                echo "</div>";
                $replyNum++;
              }

              $inqNo++;
              echo "</div>";
            }
        } else {
            echo "<p class='text-gray-700 text-center'>No inquiries found.</p>";
        }
      ?>
    </div>
  </div>

  <?php include '../includes/footer.php'; ?>

</body>
</html>
