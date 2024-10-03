<?php
    include 'config.php';


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

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registered Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="Admin">
<?php include "../includes/adminNavbar.php" ?> 

<script>
    function responsive() {
        var x = document.getElementById("content");
        x.classList.toggle("hidden");
    }
</script>

<h1 class="Users text-white text-center text-3xl mt-32 mb-10" >Registered Users</h1>
<style>
     
        .input-roup {
            position: relative;
        }
    </style>
<table class="table ">
  <thead>
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
    $query="SELECT * FROM `customer`" ;
    $query_run = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($query_run)>0){
        foreach($query_run as $row){
            ?>
            <tr>
                
                <td><?=$row['customerID'];   ?></td>
                <td><?=$row['name'];   ?></td>
                <td><?=$row['email'];   ?></td>
                
                <td><?=$row['contact'];   ?></td>
                <td><?=$row['gender'];   ?></td>
                <td><?=$row['postalCode'];   ?></td>
                <td><?=$row['houseNo'];   ?></td>
                <td><?=$row['streetName'];   ?></td>
                <td><?=$row['city'];   ?></td>
                <td><form action="delete.php" method="POST" style="display:inline;">
                        <input type="hidden" name="customerID" value="<?=$row['customerID'];?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form></td>
             </tr>
            <?php
        }
    }else{
     ?>
        <tr>
            <td colspan="6">No record Found</td>
        </tr>
     <?php

    }
    ?>
    
    
  </tbody>
</table>
</body>
</html>