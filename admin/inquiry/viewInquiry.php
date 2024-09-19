<?php

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

      <header class="bg-[#543310] h-20 z-50">
    <nav class="flex justify-between items-center w-[95%] mx-auto">
        <div class="flex items-center gap-[1vw]">
            <img class="w-16" src="../../resources/images/Logo.png" alt="Logo">
            <h1 class="text-xl text-white font-sans"><b>WOODLAK</b></h1>
            <p class="text-xl text-white font-sans">Admin</p>
        </div>
        <div class="lg:static absolute bg-[#543310] lg:min-h-fit min-h-[39vh] left-0 top-[9%] lg:w-auto w-full flex items-center px-5 justify-center lg:justify-start text-center lg:text-right xl:contents hidden lg:flex" id="content">
            <ul class="flex lg:flex-row flex-col lg:gap-[4vw] gap-8">
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../admin">Dashboard</a>
                </li>
                <li>
                    <a class="text-white  hover:text-[#D0B8A8]" href="">Products</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../orders/view_orders_Admin/OrderList.php">Orders</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../admin/inquiry">Inquiries</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../payment_process/admin_banktrans_check/admin_panel.php">Bank Transfers</a>
                </li>
                <li>
                    <a class="text-white hover:text-[#D0B8A8]" href="../UserProfile/RegisteredUsers.php">Users</a>
                </li>
            </ul>
        </div>
       
     
        <div class="flex items-center gap-3">
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]">Logout</button> 
            <button onclick="responsive()"><i class="bi bi-list text-4xl lg:hidden text-white"></i></button>
        </div>
       
    </nav>
</header>

<?php

if (isset($_GET['success'])) {
    echo '
        <div class="bg-green-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-4xl">
            <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display=\'none\';">&times;</span>
            ' . htmlspecialchars($_GET['success']) . '
        </div>';
}


if (isset($_GET['error'])) {
    echo '
        <div class="bg-red-500 text-white p-4 rounded-md mb-4 m-auto my-10 sm:max-w-4xl">
            <span class="font-bold cursor-pointer float-right text-xl leading-none" onclick="this.parentElement.style.display=\'none\';">&times;</span>
            ' . htmlspecialchars($_GET['error']) . '
        </div>';
}
?>

    <div class="flex items-center justify-center min-h-screen my-20">
        
        <div class="w-full max-w-4xl bg-translucent shadow-md rounded-lg overflow-hidden">
           
            <div class="tkt-header px-6 py-4 relative  ">
                
                <a href="inquiries.php" 
                   class="absolute top-4 left-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md text-white hover:scale-105 focus:outline-none">
                   <img src="../../resources/images/inquiry/arrow.png" alt="Back" class="w-6 h-6 mr-2">
                </a>
                <h3 class="text-2xl font-semibold text-center">Inquiry No: <?php echo $inquiry['ticketID']; ?></h3>
            </div>

            <div class="p-6">
              
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
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($inquiry['created_at']); ?></p>
                        <p><strong>Updated At:</strong> <?php echo htmlspecialchars($inquiry['updated_at']); ?></p>
                    </div>
                </div>
               
                <!-- Subject and Message -->
                <div class="mb-6 p-4 bg-transparent border border-gray-400 rounded-lg shadow-sm">
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

             
                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-4">Reply to Inquiry</h3>
                    <form method="POST" action="replyInquiry.php">
                        <input type="hidden" name="ticketID" value="<?php echo $inquiry['ticketID']; ?>">
                        <div class="mb-4">
                            <label for="reply" class="block text-gray-700">Your Reply</label>
                            <textarea name="reply" id="reply" rows="4" class="block w-full mt-1  border-b-2 border-green-500 text-gray-700 bg-transparent shadow-sm focus:bg-trancelucent focus:ring-green-500" required><?php echo isset($_POST['reply']) ? htmlspecialchars($_POST['reply']) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300">
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
                        <form method="POST" action="deleteInquiry.php"  class="w-full">
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
    <script src="../../resources/JS/navbar.js"></script>
</body>
</html>
