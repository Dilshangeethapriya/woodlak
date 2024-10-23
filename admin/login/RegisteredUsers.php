<?php
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: adminLogin.php');
    exit;
}


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];

    echo '<div class="alert alert-' . ($message_type == 'success' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">';
    echo $message;
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';

    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
		<meta name="veiwport" content="width=device-width,intial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        
    <title>Registered Users</title>
    <link rel="stylesheet" href="../../resources/css/profile/adminLogin1.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
       
        .Users {
            margin-left: 565px;
            margin-top: 10px;
            margin-bottom: 20px;
            color: white;
            font-size: 27px;
        }

        .btn-generate-report {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="Admin">
<script>
    function responsive() {
        var x = document.getElementById("content");
        x.classList.toggle("hidden");
    }
</script>

 <?php include "includes/adminNavbar.php" ?> 
<h1 class="text-center" style="font-size:30px"><b>Registered Users</b></h1>
    <style>
    .Users{
        color:brown;
        font-weight:bold;
    }
    
  </style>
  
  <div>
    
    <button class="sddc" onclick="adminPanel()" style="background-color:#78350f">Admin Panel</button>
</div>
        <style>
            .sddc{
                padding: 10px 20px;
                color: white;
                background-color: gray;
                float:right;
                margin-right:35px;
                margin-bottom:0;
                border-top-left-radius:25px;
                border-top-right-radius:25px;
                width:150px;
            }
            </style>
    <table class="table table-responsive table-hover table-striped" style="border-collapse: separate; border-spacing: 0; border: 2px solid #eaddca; border-radius: 12px; overflow: hidden;">
        <thead class="table-light">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Contact</th>
                <th scope="col">Gender</th>
                <th scope="col">Postal Code</th>
                <th scope="col">House No</th>
                <th scope="col">Street Name</th>
                <th scope="col">City</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM `customer`";
            $query_run = mysqli_query($conn, $query);

            if (mysqli_num_rows($query_run) > 0) {
                foreach ($query_run as $row) {
                    ?>
                    <tr>
                        <td><?=$row['customerID'];?></td>
                        <td><?=$row['name'];?></td>
                        <td><?=$row['email'];?></td>
                        <td><?=$row['contact'];?></td>
                        <td><?=$row['gender'];?></td>
                        <td><?=$row['postalCode'];?></td>
                        <td><?=$row['houseNo'];?></td>
                        <td><?=$row['streetName'];?></td>
                        <td><?=$row['city'];?></td>
                        <td>
                            <form action="delete.php" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                <input type="hidden" name="customerID" value="<?=$row['customerID'];?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="10">No record Found</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    
    <form action="generate_report.php" method="POST">
    <button type="submit" name="generate_report" class="btn btn-primary btn-generate-report">Generate Report</button>
</form>


    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }
        function adminPanel() {
        window.location.href = 'adminPanel.php'; 
    }

    </script>


</body>
</html>
