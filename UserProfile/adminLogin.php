<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $select = mysqli_query($conn, "SELECT * FROM `admin` WHERE email = '$email' AND password = '$password'") or die('query failed');

    if(mysqli_num_rows($select) > 0){
        $row = mysqli_fetch_assoc($select);
        $_SESSION['user_id'] = $row['adminID'];
        header('location:../admin/');
    }else{
        $message[] = ' Incorrect email or password ';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
        <style>
            body{
                font-family: sans-serif;
            }
        </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

               
    <div class="register-container">
        <form action="adminLogin.php" method="POST" class="register-form" enctype="multipart/form-data">
            <h2>ADMIN LOGIN</h2>
            <?php

            if(isset($message)){
                foreach($message as $message){
                    echo '<div class="message">'.$message.'</div>';
                }
            }
            ?>
            
            <div class="input-group">
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            
            <button type="submit" name="submit" class="btn">Login Now</button>
            
            <p>Don't have an account? <a href="adminRegister.php">Register as Admin</a></p>
        </form>
    </div>
    <script>
            function responsive() {
                var x = document.getElementById("content");
                if (x.classList.contains("hidden")) {
                    x.classList.remove("hidden");
                } else {
                    x.classList.add("hidden");
                }
            }
        </script>
</body>
</html>