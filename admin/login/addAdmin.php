<?php
include 'config.php';  
session_start(); 

$name = $email = $password = $type = '';
$message = '';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['text']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);  

  
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format!";
        $_SESSION['message_type'] = 'danger'; 
        header('Location: addAdmin.php');  
        exit();
    }

    $email_check_query = "SELECT * FROM admin WHERE email = '$email' LIMIT 1";
    $email_check_result = mysqli_query($conn, $email_check_query);
    
    if (mysqli_num_rows($email_check_result) > 0) {
        $_SESSION['message'] = "The email address is already registered.";
        $_SESSION['message_type'] = 'danger'; 
        header('Location: addAdmin.php');  
        exit();
    } else {

        $insert_query = "INSERT INTO admin (name, email, password, type) VALUES ('$name', '$email', '$password', '$type')";

        if (mysqli_query($conn, $insert_query)) {
            
            $adminID = mysqli_insert_id($conn);

            $_SESSION['user_id'] = $adminID;

            $_SESSION['message'] = "Admin added successfully!";
            $_SESSION['message_type'] = 'success'; 
            
            header('Location: adminLogin.php'); 
            exit();
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
            $_SESSION['message_type'] = 'danger'; 
            header('Location: addAdmin.php');  
            exit();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="cart.css">
    <style>
        body {
            font-family: sans-serif;
        }
        .register-container {
            width: 100%;
            max-width: 500px;
            margin: 50px auto;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #543310;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .message {
            padding: 10px;
            background-color: #f2dede;
            color: #a94442;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<?php include 'includes/adminNavbar.php' ?>

<div class="register-container">
    <form action="addAdmin.php" method="POST" class="register-form" enctype="multipart/form-data">
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php 
            unset($_SESSION['message']);  
            unset($_SESSION['message_type']); 
            ?>
        <?php endif; ?>
        
        <div class="input-group">
            <h4 class="name"> Name </h4>
            <input type="text" name="text" value="<?php echo $name; ?>" required>
        </div>
        <div class="input-group">
            <h4 class="email"> Email Address </h4>
            <input type="email" name="email" value="<?php echo $email; ?>" required>
        </div>
        <div class="input-group">
            <h4 class="password"> Password </h4>
            <input type="password" name="password" required>
        </div>
        <div class="input-group">
            <h4 class="type"> Admin Type </h4>
            <select name="type" required>
                <option value="Customer Management" <?php echo ($type == 'superadmin') ? 'selected' : ''; ?>>Customer Management</option>
                <option value="Stock Balance" <?php echo ($type == 'admin') ? 'selected' : ''; ?>>Stock Balance</option>
                <option value="Order Management" <?php echo ($type == 'moderator') ? 'selected' : ''; ?>>Order Management</option>
                <option value="Payment Management" <?php echo ($type == 'admin') ? 'selected' : ''; ?>>Payment Management</option>
                <option value="Reviews Handling" <?php echo ($type == 'admin') ? 'selected' : ''; ?>>Reviews Handling</option>
            </select>
        </div>
        
        <button type="submit" name="submit" class="btn">Add as a new Admin</button>
    </form>
</div>

</body>
</html>
