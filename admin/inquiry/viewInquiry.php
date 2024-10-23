<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

include('../../config/dbconnect.php');


if (isset($_GET['id'])) {
    
    $ticketID = intval($_GET['id']); 


    $query = "SELECT * FROM tickets WHERE ticketID = ?";
    
  
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $ticketID);  
        $stmt->execute();  
        $result = $stmt->get_result();  
        

        if ($result->num_rows > 0) {
            $inquiry = $result->fetch_assoc(); 
        } else {
            
            echo "Inquiry not found!";
            exit;
        }
        $stmt->close();  
    }


    // Query to get replies associated with the inquiry
    $repliesQuery = "SELECT * FROM ticket_replies WHERE ticketID = ? ORDER BY created_at ASC";

    $replies = [];
    if ($repliesStmt = $conn->prepare($repliesQuery)) {
        $repliesStmt->bind_param("i", $ticketID);
        $repliesStmt->execute();
        $repliesResult = $repliesStmt->get_result();

        if ($repliesResult->num_rows > 0) {
            while ($row = $repliesResult->fetch_assoc()) {
                $replies[] = $row;
            }
        }
        $repliesStmt->close();
    }

} else {

    header("Location: inquiries.php");
    exit;
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../../resources/css/admin/inquiries.css">
</head>
<body class="font-sans text-gray-900 antialiased">

    <?php include "../../includes/adminNavbar.php" ?> 

    <div class="flex items-center justify-center min-h-screen my-20">
        
        <div class="w-full max-w-4xl bg-translucent shadow-md rounded-lg overflow-hidden">
           
            <div class="tkt-header px-6 py-4 relative">
                <a href="inquiries.php" 
                   class="absolute top-4 left-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md text-white hover:scale-105 focus:outline-none">
                   <img src="../../resources/images/inquiry/arrow.png" alt="Back" class="w-6 h-6 mr-2">
                </a>
                <h3 class="text-2xl font-semibold text-center">Inquiry No: <?php echo $inquiry['ticketID']; ?></h3>
            </div>

            <div class="p-6">

            <!-- success and error msg -->
            <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                        <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display='none';">&times;</span>
                        <?php echo $_SESSION['success']; ?>
                    </div>
                    <?php unset($_SESSION['success']); // Unset the message after displaying it ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-500 text-white p-4 rounded-md mb-4">
                        <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display='none';">&times;</span>
                        <?php echo $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']); // Unset the message after displaying it ?>
                <?php endif; ?>
              

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($inquiry['name']); ?></p>
                    </div>
                    <div>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($inquiry['email']); ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($inquiry['phone']); ?></p>
                    </div>
                    <div>
                        <p><strong>Created Date:</strong> 
                           <?php 
                           $created_at = new DateTime($inquiry['created_at']);
                           $inquiryCreatedDate = $created_at->format('F j, Y, g:i A');
                           echo $inquiryCreatedDate; 
                           ?>
                        </p>
                        <p><strong>Updated On:</strong> 
                          <?php 
                            $updated_at = new DateTime($inquiry['updated_at']);
                            $inquiryUpdatedDate = $updated_at->format('F j, Y, g:i A');
                             echo $inquiryUpdatedDate; 
                          ?>
                          </p>
                    </div>
                </div>
               
                <!-- Subject and Message -->
                <div class="mb-6 p-4 bg-transparent border border-green-600 backdrop-blur-md bg-blur-sm rounded-lg shadow-sm">
                    <div class="mb-6 text-center">
                        <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($inquiry['subject']); ?></h3>
                    </div>
                    <p class="text-gray-600"><?php echo htmlspecialchars($inquiry['ticketText']); ?></p>
                </div>

                <!-- Inquiry Status -->
                <div class="mb-6">
                    <span class="inline-block px-4 py-2 text-lg font-semibold text-white
                        <?php if ($inquiry['ticketStatus'] == 'New') echo 'bg-blue-600'; 
                              elseif ($inquiry['ticketStatus'] == 'In Progress') echo 'bg-yellow-500'; 
                              elseif ($inquiry['ticketStatus'] == 'Closed') echo 'bg-green-600'; ?>">
                        <?php echo htmlspecialchars($inquiry['ticketStatus']); ?>
                    </span>
                </div>

          <!-- Replies section -->
<div class="mt-6 bg-transparent">
    <h3 class="text-xl font-semibold mb-4 bg-transparent">Old Replies</h3>
    <?php if (count($replies) > 0): ?>
        <ul class="space-y-4">
            <?php foreach ($replies as $reply): ?>
                <?php
                $date = new DateTime($reply['created_at']);
                $formattedDate = $date->format('F j, Y, g:i A');
                ?>
                <li class=" border border-green-600 backdrop-blur-md p-4 rounded-lg shadow-sm  bg-[rgba(240,248,255,0.1)] mt-4 p-3 rounded-lg border-l-4 border-l-[#38b2ac] border-solid">
                    <div class="mb-2 text-[#74512D] font-semibold">
                        replied on: <?php echo $formattedDate; ?>:
                    </div>
                    <p class="text-black"><?php echo htmlspecialchars($reply['replyText']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-gray-600">No replies sent for this inquiry.</p>
    <?php endif; ?>
</div>


             
                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-4">Send a Reply</h3>
                    <form method="POST" action="replyInquiry.php">
                        <input type="hidden" name="ticketID" value="<?php echo $inquiry['ticketID']; ?>">
                        <div class="mb-4">
                            <textarea name="reply" id="reply" rows="4" class="block .placeholder-black::placeholder w-full mt-1  border border-green-600 text-gray-700 bg-[rgba(240,248,255,0.6)] shadow-sm focus:bg-trancelucent focus:ring-green-500 rounded-lg p-2" placeholder="Your Reply" required><?php echo isset($_POST['reply']) ? htmlspecialchars($_POST['reply']) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600  border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300">
                            Send Reply
                        </button>
                    </form>
                </div>

           
                <div class="bg-transparent p-6 mt-5 flex justify-between items-end">
                    <div class="inline-flex w-2/3">
                        <form method="POST" action="updateInquiryStatus.php" class="w-full">
                            <input type="hidden" name="ticketID" value="<?php echo $inquiry['ticketID']; ?>">
                            <label for="status" class="mb-4 block text-gray-700">Change Status</label>
                            <select name="status" id="status" class="mr-5 w-1/3 py-3 sm:py-3 md:py-1  rounded border-gray-300 text-gray-700 shadow-sm focus:ring-indigo-500">
                                <option value="New" <?php if ($inquiry['ticketStatus'] == 'New') echo 'selected'; ?>>New</option>
                                <option value="In Progress" <?php if ($inquiry['ticketStatus'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                <option value="Closed" <?php if ($inquiry['ticketStatus'] == 'Closed') echo 'selected'; ?>>Closed</option>
                            </select>
                            <button type="submit" class=" w-1/3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300">
                                Update Status
                            </button>
                        </form>
                    </div>

                    <?php if ($inquiry['ticketStatus'] == 'Closed'): ?>
                    <div class="inline-flex w-1/3">
                        <form method="POST" action="deleteInquiry.php"  class="w-full" onsubmit="return confirmDelete();">
                            <input type="hidden" name="ticketID" value="<?php echo $inquiry['ticketID']; ?>">
                            <button type="submit" class=" mt-auto w-2/3 px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring ring-red-300">
                                Delete Inquiry
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this inquiry? This action cannot be undone.");
    }
</script>
</body>
</html>
