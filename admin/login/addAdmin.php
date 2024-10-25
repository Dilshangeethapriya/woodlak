<?php

include 'config.php';
session_start(); 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: adminLogin.php');
    exit;
}

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

    
    if (strlen($password) < 8 || !preg_match('/[!@#$%^&*]/', $password) || !preg_match('/[0-9]/', $password)) {
        $_SESSION['message'] = "Password must be at least 8 characters long, contain at least one special character, and one number!";
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
            $_SESSION['message'] = "Admin added successfully!";
            $_SESSION['message_type'] = 'success'; 
            header('Location: adminPanel.php'); 
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
    <link rel="stylesheet" href="../../resources/css/profile/adminLogin1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: sans-serif;
        }
        .register-container {
            width: 100%;
            max-width: 500px;
            margin: -20px auto;
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
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <div class="input-group">
            <input type="text" name="text" placeholder="Enter Admin Name" value="<?php echo $name; ?>" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Enter Admin Email" value="<?php echo $email; ?>" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Enter Admin Password" value="<?php echo $password; ?>" required>
        </div>
        <div class="input-group">
            <select name="type" required>
                <option value="" disabled selected>Select Admin Type</option>
                <option value="superadmin">Super Admin</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="submit" class="btn">Add Admin</button>
    </form>
</div>

</body>
</html>
