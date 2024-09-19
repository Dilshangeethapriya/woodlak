<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = [];

if (isset($_POST['saveProfile'])) {
    $edit_name = mysqli_real_escape_string($conn, $_POST['edit_name']);
    $edit_email = mysqli_real_escape_string($conn, $_POST['edit_email']);
    $edit_contact = mysqli_real_escape_string($conn, $_POST['edit_contact']);
    $edit_gender = mysqli_real_escape_string($conn, $_POST['edit_gender']);
    $edit_houseNo = mysqli_real_escape_string($conn, $_POST['edit_houseNo']);
    $edit_streetName = mysqli_real_escape_string($conn, $_POST['edit_streetName']);
    $edit_city = mysqli_real_escape_string($conn, $_POST['edit_city']);
    $edit_postalCode = mysqli_real_escape_string($conn, $_POST['edit_postalCode']);

    if (!preg_match('/^[0-9]{5}$/', $edit_postalCode)) {
        $message[] = 'Postal code must be exactly 5 digits.';
    }
    if (!preg_match('/^[0-9]{10}$/', $edit_contact)) {
        $message[] = 'Invalid contact number. ';
    } else {
        if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] == 0) {
            $file_tmp = $_FILES['edit_image']['tmp_name'];
            $file_name = basename($_FILES['edit_image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid('', true) . '.' . $file_ext;
            $upload_dir = 'uploaded_img/';
            $upload_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_file)) {
                $image_query = ", image='$new_file_name'";
            } else {
                $image_query = '';
            }
        } else {
            $image_query = '';
        }

        $update_query = "UPDATE `customer` SET 
            name='$edit_name', 
            email='$edit_email', 
            contact='$edit_contact',
            postalCode='$edit_postalCode', 
            houseNo='$edit_houseNo', 
            streetName='$edit_streetName', 
            city='$edit_city', 
            gender='$edit_gender'" . 
            $image_query . 
            " WHERE customerID='$user_id'";

        if (!mysqli_query($conn, $update_query)) {
            $message[] = 'Query failed: ' . mysqli_error($conn);
        } else {
            $message[] = 'Profile Saved successfully!';
        }
    }
}


if (!empty($_POST['update_pw']) || !empty($_POST['new_pw']) || !empty($_POST['confirm_pw'])) {
    $old_pw = $_POST['old_pw'];
    $update_pw = $_POST['update_pw'];
    $new_pw = $_POST['new_pw'];
    $confirm_pw = $_POST['confirm_pw'];

    $select = mysqli_query($conn, "SELECT password FROM `customer` WHERE customerID='$user_id'") or die('Query failed');
    $fetch = mysqli_fetch_assoc($select);

    if (!($old_pw== $fetch['password'])) {
        $message[] = 'Old password does not match!';
    } elseif ($new_pw !== $confirm_pw) {
        $message[] = 'New password and confirmation do not match!';
    } else {
        //$hashed_pw = password_hash($confirm_pw, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE `customer` SET password='$confirm_pw' WHERE customerID='$user_id'") or die('Query failed: ' . mysqli_error($conn));
        $message[] = 'Password Saved successfully!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="nav.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
        <style>
            body{
                font-family: sans-serif;
            }
        </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        function goBack() {
            window.location.href = 'Profile.php';
        }
    </script>
</head>
<body>
<header class="bg-[#543310] h-20 ">
            <nav class="flex justify-between items-center w-[95%] mx-auto">
                <div class="flex items-center gap-[1vw]">
                    <img class="w-16" src="Logo.png" alt="Logo">
                    <h1 class="text-xl text-white"><b>WOODLAK</b></h1>
                </div>
                <div class="lg:static absolute bg-[#543310] lg:min-h-fit min-h-[39vh] left-0 top-[9%] lg:w-auto w-full flex items-center px-5 justify-center lg:justify-start items-center lg:items-start text-center lg:text-right xl:contents hidden" id="content" >
                    <ul class="flex lg:flex-row flex-col  lg:gap-[4vw] gap-8">
                        <li>
                            <a class="text-white hover:text-[#D0B8A8] " href="../">Home</a>
                        </li>
                        <li>
                            <a class="text-white hover:text-[#D0B8A8]" href="../inquiry">Contact Us</a>
                        </li>
                        <li>
                            <a class="text-white hover:text-[#D0B8A8]" href="#">About Us</a>
                        </li>
                        <li>
                            <a class="text-white hover:text-[#D0B8A8]" href="../product/product_catalog.php">Products</a>
                        </li>
                        <li>
                            <a class="text-white hover:text-[#D0B8A8]" href="#">Orders</a>
                        </li>
                    </ul>
                </div>
                <?php 
        if(isset($_SESSION['user_name'])) {
            $user_name = $_SESSION['user_name'];
        ?>
        <div class="flex items-center gap-3">
            <span class="mr-4 text-lg"><?php echo $user_name; ?></span>
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='/dashboard/woodlak/public/Userprofile/profile.php'">Profile</button> 
            <button onclick="responsive()"><i class="bi bi-list text-4xl lg:hidden text-white"></i></button>
        </div>
        <?php } else { ?>
        <div class="flex items-center gap-3">
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='/dashboard/woodlak/public/Userprofile/register.php'">Register</button>
            <button class="bg-[#74512D] text-white px-5 py-2 rounded-full hover:text-[#D0B8A8]" onclick="location.href='/dashboard/woodlak/public/Userprofile/login.php'">Login</button>
            <button onclick="responsive()"><i class="bi bi-list text-4xl lg:hidden text-white"></i></button>
        </div>
        <?php } ?>
    </nav>
        </header>
<div class="profile">
    <?php
        $select = mysqli_query($conn, "SELECT * FROM `customer` WHERE customerID='$user_id'") or die('Query failed');
        if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
        }
    ?>

    <form action="EditProfile.php" method="post" enctype="multipart/form-data">
    <div class="greeting flex flex-col justify-center items-center">
    <?php
            if (isset($fetch['image']) && $fetch['image'] != '') {
                echo '<img src="uploaded_img/'.$fetch['image'].'" alt="Profile Picture">';
            } else {
                echo '<img src="images/avatar.png" alt="Default Avatar">';
            }
            
            if (!empty($message)) {
                foreach ($message as $msg) {
                    echo '<div class="message">'.$msg.'</div>';
                }
            }
        ?> 
</div>
      
        <div class="flex">
            <div class="input-roup">
                <span>Username: </span>
                <input type="text" name="edit_name" value="<?php echo htmlspecialchars($fetch['name']); ?>" class="box">
       
                <span>Email Address: </span>
                <input type="email" name="edit_email" value="<?php echo htmlspecialchars($fetch['email']); ?>" class="box">
                
                <span>Contact number: </span>
                <input type="text" name="edit_contact" value="<?php echo htmlspecialchars($fetch['contact']); ?>" class="box">
                
                <span>Gender: </span>
                <select name="edit_gender" class="box">
                    <option value="Male" <?php if ($fetch['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($fetch['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
                <span>Profile picture: </span>
                <input type="file" name="edit_image" accept="image/jpg, image/jpeg, image/png" class="box">
                <span>Postal Code: </span>
<input type="text" name="edit_postalCode" value="<?php echo htmlspecialchars($fetch['postalCode']); ?>" class="box" pattern="\d{5}" maxlength="5" required title="Postal code must be exactly 5 digits">

            </div>
            
            <div class="input-roup">
                <span>HouseNo: </span>
                <input type="text" name="edit_houseNo" value="<?php echo htmlspecialchars($fetch['houseNo']); ?>" class="box">
                <span>Street Name: </span>
                <input type="text" name="edit_streetName" value="<?php echo htmlspecialchars($fetch['streetName']); ?>" class="box">
                <span>City: </span>
                <input type="text" name="edit_city" value="<?php echo htmlspecialchars($fetch['city']); ?>" class="box">
                
                <input type="hidden" name="old_pw" value="<?php echo htmlspecialchars($fetch['password']); ?>">
                
                <span>Current Password: </span>
                <input type="password" name="update_pw" placeholder="Enter current password" class="box">
                
                <span>New Password: </span>
                <input type="password" name="new_pw" placeholder="Enter new password" class="box">
                
                <span>Confirm Password: </span>
                <input type="password" name="confirm_pw" placeholder="Re-enter new password" class="box">
            </div>
        </div>
        <button type="submit" name="saveProfile" class="btn">Save Profile</button><p></p>
        
        <button type="button" class="btn" onclick="goBack()">Go Back</button>
    </form>
</div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const postalCodeInput = document.querySelector('input[name="edit_postalCode"]');
        postalCodeInput.addEventListener('input', function () {
            if (this.value.length > 5) {
                this.value = this.value.slice(0, 5);
            }
        });
    });
    function responsive() {
                var x = document.getElementById("content");
                if (x.classList.contains("hidden")) {
                    x.classList.remove("hidden");
                } else {
                    x.classList.add("hidden");
                }
            }
</script>
