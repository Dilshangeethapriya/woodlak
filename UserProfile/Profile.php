<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="nav.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
        integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style2.css">
    <style>
        body{
                font-family: sans-serif;
            }
        .eye-icon {
            position: absolute;
            margin-left:65px; 
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: black;
        }

        .input-roup {
            position: relative;
        }
    </style>
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
        include 'config.php';
        session_start();
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
        

        $user_id = $_SESSION['user_id'];
        $select = mysqli_query($conn, "SELECT * FROM `customer` WHERE customerID = '$user_id'") or die('Query failed');
        if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
        }
    ?>

    <form action="EditProfile.php" method="get">

<div class="greeting flex flex-col justify-center items-center w-full">
    <p class="max-w-full text-center mb-4">Hello, <?php echo htmlspecialchars($fetch['name']); ?>,</p>
    <?php
    if ($fetch['image'] == '') {
        echo '<img src="images/avatar.png" alt="Profile Picture" class="w-24 h-24 rounded-full">';
    } else {
        echo '<img src="uploaded_img/'.$fetch['image'].'" alt="Profile Picture" class="w-24 h-24 rounded-full">';
    }
    ?>
</div>



       
        
        <div class="flex">
            <div class="input-roup">
                <span class="span">Username: </span>
                <input type="text" name="profile_name" value="<?php echo htmlspecialchars($fetch['name']); ?>" class="box" readonly>
                <span>Email Address: </span>
                <input type="email" name="profile_email" value="<?php echo htmlspecialchars($fetch['email']); ?>" class="box" readonly>
                <span>Contact : </span>
                <input type="text" name="profile_contact" value="<?php echo htmlspecialchars($fetch['contact']); ?>" class="box" readonly>
            </div>


            <div class="input-roup">
                <span class="span">Postal Code: </span>
                <input type="text" name="profile_postalCode" value="<?php echo htmlspecialchars($fetch['postalCode']); ?>" class="box" readonly>
                <span class="span">HouseNo: </span>
                <input type="text" name="profile_houseNo" value="<?php echo htmlspecialchars($fetch['houseNo']); ?>" class="box" readonly>
                <span>Street Name: </span>
                <input type="email" name="profile_streetName" value="<?php echo htmlspecialchars($fetch['streetName']); ?>" class="box" readonly>
            </div>

            <div class="input-roup">
                <span>City: </span>
                <input type="text" name="profile_city" value="<?php echo htmlspecialchars($fetch['city']); ?>" class="box" readonly>
                <span>Gender: </span>
                <input type="text" name="profile_gender" value="<?php echo htmlspecialchars($fetch['gender']); ?>" class="box" readonly>
                <span>Password: </span>
                <div class="input-roup">
                    <input type="password" id="profile_password" name="profile_password" value="<?php echo htmlspecialchars($fetch['password']); ?>" class="box" readonly style="width:125px">
                    <i class="fas fa-eye eye-icon" id="togglePassword" onclick="togglePassword()"></i>
                </div>
            </div>
        </div>
        
        <button type="submit" name="editProfile" class="btn">Edit Profile</button>
        <p></p>
        <button type="button" class="btn">Order History</button>
        <p></p>
        <button type="button" id="logoutButton" class="btn">LogOut</button>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('profile_password');
        const eyeIcon = document.getElementById('togglePassword');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    document.getElementById('logoutButton').addEventListener('click', function() {
        window.location.href = 'logout.php'; 
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
</body>
</html>
