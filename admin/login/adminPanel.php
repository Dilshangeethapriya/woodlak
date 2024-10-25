<?php
session_start();
include 'config.php';  

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: adminLogin.php');
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
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
        .add-admin-container {
            text-align: left;
            margin: 20px; 
            width:120px;
            margin-left: 1200px;
            margin-bottom:1px;
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

 <?php 
   if(isset($_SESSION['message'])){
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

<h1 class="text-center mt-10" style="font-size:30px"><b>Admin Panel</b></h1>
  <style>
    .Users{
        color:black;
        font-weight:bold;
    }
  </style>
  
  <div>
  <button class="sddc" onclick="addAdmin()" style="background-color:#78350f">+ Add Admin</button>
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
            <th scope="col">Admin ID</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Admin Type</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM `admin`";
        $query_run = mysqli_query($conn, $query);

        if(mysqli_num_rows($query_run) > 0){
            foreach($query_run as $row){
                ?>
                <tr>
                    <td><?=$row['adminID'];?></td>
                    <td><?=$row['name'];?></td>
                    <td><?=$row['email'];?></td>
                    <td><?=$row['type'];?></td>
                    <td>
                        <form action="adminDelete.php" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                            <input type="hidden" name="adminID" value="<?=$row['adminID'];?>">
                            <button type="submit" class="btn btn-danger">Remove Admin</button>
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

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this admin?");
    }
    function addAdmin() {
        window.location.href = 'addAdmin.php';  
    }

</script>

</body>
</html>


