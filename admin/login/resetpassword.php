<?php
include 'config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  
    header('Location: adminLogin.php');
    exit;
}

$message = '';

if (isset($_POST['submit_password'])) {
    $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    $admin_id = $_SESSION['admin_id'];

    $query = "SELECT * FROM `admin` WHERE adminID = '$admin_id'";
    $result = mysqli_query($conn, $query) or die('Query failed');
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        if ($old_password === $row['password']) {
            
            // Server-side validation for password strength
            if (strlen($new_password) < 8 || !preg_match('/[!@#$%^&*]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
                $message = 'New password must be at least 8 characters long, contain at least one special character, and one number!';
            } elseif ($new_password === $confirm_password) {
                
                $update_query = "UPDATE `admin` SET password = '$new_password' WHERE adminID = '$admin_id'";
                $update_result = mysqli_query($conn, $update_query);
                
                if ($update_result) {
                    $message = 'Password updated successfully!';
                    // Redirect to adminLogin.php after a successful password update
                    header('Location: adminLogin.php');
                    exit;
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
    <link rel="stylesheet" href="../../resources/css/profile/adminLogin1.css">
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

    <!-- JavaScript for password validation -->
    <script>
        function validateForm() {
            const newPassword = document.querySelector('input[name="new_password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            // Regex: At least 8 characters, one number, and one special character
            const passwordPattern = /^(?=.*[0-9])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

            // Check if new password meets criteria
            if (!passwordPattern.test(newPassword)) {
                alert("New password must be at least 8 characters long, contain at least one special character, and one number.");
                return false;
            }

            // Check if new password matches confirm password
            if (newPassword !== confirmPassword) {
                alert("New password and confirm password do not match!");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>

<div class="form-container mt-44">
    <form action="resetPassword.php" method="POST" class="change-password-form" onsubmit="return validateForm();">
        
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
