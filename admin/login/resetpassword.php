<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: adminLogin.php');
    exit;
}

$message = '';

if (isset($_POST['submit_password'])) {
    $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    $admin_id = $_SESSION['user_id'];

    $query = "SELECT * FROM `admin` WHERE adminID = '$admin_id'";
    $result = mysqli_query($conn, $query) or die('Query failed');
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        if ($old_password === $row['password']) {
            
            if ($new_password === $confirm_password) {
                
                $update_query = "UPDATE `admin` SET password = '$new_password' WHERE adminID = '$admin_id'";
                $update_result = mysqli_query($conn, $update_query);
                
                if ($update_result) {
                    $message = 'Password updated successfully!';
                } else {
                    $message = 'Error updating password. Please try again.';
                }
            } else {
                $message = 'New password and confirm password do not match!';
            }
        } else {
            $message = 'Old password is incorrect!';
        }
    } else {
        $message = 'User not found!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: sans-serif;
        }
        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 50px auto;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #550f0a;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
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

<div class="form-container">
    <form action="resetPassword.php" method="POST" class="change-password-form" onsubmit="return validateForm()">
        
        <?php if ($message != ''): ?>
            <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'danger' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="input-group">
            <h4>Old Password</h4>
            <input type="password" name="old_password" placeholder="Enter current password" required>
        </div>
        <div class="input-group">
            <h4>New Password</h4>
            <input type="password" name="new_password" placeholder="Enter new password" required>
        </div>
        <div class="input-group">
            <h4>Confirm New Password</h4>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
        </div>
        
        <button type="submit" name="submit_password" class="btn">Update Password</button>
    </form>

    <form action="adminLogin.php" method="GET">
        <button type="submit" class="btn">Cancel</button>
    </form>
</div>

</body>
</html>
