<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: http://localhost/woodlak/admin/login/adminLogin.php');
    exit;
}

include('../../config/dbconnect.php');


if (isset($_GET['id'])) {
 
    $callbackID = intval($_GET['id']); 

   
    $query = "SELECT * FROM callback_requests WHERE id = ?";
    
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $callbackID);  
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        
      
        if ($result->num_rows > 0) {
            $callback = $result->fetch_assoc(); 
        } else {
           
            echo "Callback request not found!";
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
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Callback Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../resources/css/admin/inquiries.css">

   
</head>
<body  >
    <?php include "../../includes/adminNavbar.php" ?> 
    <main class="container" style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div class="mt-32 shadow-lg rounded-lg overflow-hidden">
            <div class="tkt-header px-6 py-4 relative">
                <a href="inquiries.php" 
                   class="absolute top-4 left-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md text-white hover:scale-105 focus:outline-none">
                   <img src="../../resources/images/inquiry/arrow.png" alt="Back" class="w-6 h-6 mr-2">
                </a>
                <h3 class="text-2xl font-semibold text-center">Callback Request No: <?php echo $callback['id']; ?></h3>
            </div>

            <div class="px-6 py-4 border-b bg-translucent">
                <div class="space-y-4">
                    <div style="display: flex; justify-content: space-between;">
                        <strong>Name:</strong>
                        <span><?php echo $callback['name']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <strong>Phone:</strong>
                        <span><?php echo $callback['phone']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <strong>Available Time:</strong>
                        <span><?php echo $callback['time_from']; ?> - <?php echo $callback['time_to']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <strong>Requested At:</strong>
                        <span><?php echo $callback['created_at']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <strong>Status:</strong>
                        <span style="font-weight: bold; color:
                            <?php 
                                if ($callback['status'] == 'Pending') echo '#3B82F6';
                                elseif ($callback['status'] == 'In Progress') echo '#FBBF24';
                                elseif ($callback['status'] == 'Failed') echo '#DC2626';
                                elseif ($callback['status'] == 'Completed') echo '#10B981'; 
                            ?>">
                            <?php echo $callback['status']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex items-center bg-translucent border-t btn-container">
                <form method="POST" action="updateCallbackStatus.php" class="flex w-2/3">
                    <input type="hidden" name="id" value="<?php echo $callback['id']; ?>">
                    <select id="status" name="status" class="block mr-4 py-2  rounded-md shadow-sm border-gray-300 focus:ring focus:ring-opacity-50 w-1/2">
                        <option value="Pending" <?php echo $callback['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="In Progress" <?php echo $callback['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Failed" <?php echo $callback['status'] == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                        <option value="Completed" <?php echo $callback['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 btn-update w-1/2">
                        Update Status
                    </button>
                </form>

                <form method="POST" action="deleteCallback.php" class="flex-shrink-0" onsubmit="return confirmDelete();">
                    <input type="hidden" name="id" value="<?php echo $callback['id']; ?>">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:border-red-700 focus:ring ring-red-300
                            <?php echo ($callback['status'] == 'Completed' || $callback['status'] == 'Failed') ? 'btn-enabled' : 'btn-disabled'; ?>"
                            <?php echo ($callback['status'] != 'Completed' && $callback['status'] != 'Failed') ? 'disabled' : ''; ?>>
                        Delete Request
                    </button>
                </form>
            </div>
        </div>
    </main>
    <script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this callback request? This action cannot be undone.");
    }
</script>
</body>
</html>
