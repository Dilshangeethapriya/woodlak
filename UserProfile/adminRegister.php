<?php

include 'config.php';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    } elseif (strlen($password) < 10) {
        $message[] = 'Password must be at least 10 characters long!';
    } else {
        $select = mysqli_query($conn, "SELECT * FROM `admin` WHERE email = '$email'") or die('query failed');

        if (mysqli_num_rows($select) > 0) {
            $message[] = 'User with this email already exists!';
        } else {
            if ($password != $confirm_password) {
                $message[] = 'Confirm password does not match!';
            } else {
                $insert = mysqli_query($conn, "INSERT INTO `admin`(name, email, password) VALUES('$name', '$email', '$password')") or die('query failed');

                if ($insert) {
                 
                    $message[] = 'Registered Successfully!';
                    header('location:adminLogin.php');
                    exit(); 
                } else {
                    $message[] = 'Registration Failed!';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
       
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

                         
    <div class="register-container">
        <form action="adminRegister.php" method="POST" class="register-form" enctype="multipart/form-data">
            <h2>ADMIN REGISTER</h2>
            <?php
            if (isset($message)) {
                foreach ($message as $message) {
                    echo '<div class="message">' . $message . '</div>';
                }
            }
            ?>
            <div class="input-group">
                <input type="text" name="name" placeholder="Admin Name" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Admin Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Admin Password" required>
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Re-Enter Password" required>
            </div>
            
            <button type="submit" name="submit" class="btn">Admin Register </button>
            <p>Already have an account? <a href="adminLogin.php">Login Now as Admin</a></p>
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
