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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registered Users</title>
    <link rel="stylesheet" href="style.css">
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
 
<!-- <?php include '../includes/navbar.php' ?> -->

<h1 class="Users">Admin Panel</h1>
  <style>
    .Users{
        color:black;
        font-weight:bold;
    }
  </style>
  
<div class="add-admin-container">
    <a href="addAdmin.php" class="btn btn-primary btn-sm">+ Add Admin</a>
    
</div>

<table class="table">
    <thead>
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
</script>

</body>
</html>


