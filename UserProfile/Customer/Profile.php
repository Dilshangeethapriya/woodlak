<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
    
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
        integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style2.css">
    <style>
        body {
            font-family: sans-serif;
        }

        .eye-icon {
            position: absolute;
            margin-left: 65px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: black;
        }

        .input-roup {
            position: relative;
        }

        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .profile img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 10px auto;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .flex {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .btn {
            background-color: #550f0a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 150px; 
            height: 45px; 
            text-align: center;
        }

        .btn:hover {
            background-color: #911911;
        }

        .space-x-0 {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header class="bg-[#543310] h-20 relative">
        <nav class="flex justify-between items-center w-[95%] mx-auto">
            <div class="flex items-center gap-[1vw]">
                <img class="w-16" src="Logo.png" alt="Logo">
                <h1 class="text-xl text-white"><b>WOODLAK</b></h1>
            </div>
            <div class="lg:static absolute bg-[#543310] lg:min-h-fit min-h-[39vh] left-0 top-[9%] lg:w-auto w-full flex items-center px-5 justify-center lg:justify-start items-center lg:items-start text-center lg:text-right xl:contents" id="content">
                <ul class="flex lg:flex-row flex-col lg:gap-[4vw] gap-8">
                    <li><a class="text-white hover:text-[#D0B8A8]" href="../../">Home</a></li>
                    <li><a class="text-white hover:text-[#D0B8A8]" href="../../inquiryMgt/ContactUs.php">Contact Us</a></li>
                    <li><a class="text-white hover:text-[#D0B8A8]" href="#">About Us</a></li>
                    <li><a class="text-white hover:text-[#D0B8A8]" href="">Products</a></li>
                    <li><a class="text-white hover:text-[#D0B8A8]" href="#">Orders</a></li>
                    
                </ul>

            </div>
<div class="flex items-center gap-3">
<li><a class="text-white hover:text-[#D0B8A8]" href="logout.php">LogOut</a></li>
    </div>
   
        </nav>
    </header>

    <div class="profile">
        <form action="EditProfile.php" method="get">
            <?php
                include 'config.php';
                session_start();
                $user_id = $_SESSION['user_id'];
                $select = mysqli_query($conn, "SELECT * FROM `customer` WHERE customerID = '$user_id'") or die('Query failed');
                if (mysqli_num_rows($select) > 0) {
                    $fetch = mysqli_fetch_assoc($select);
                }
            ?>

            <?php
                if ($fetch['image'] == '') {
                    echo '<img src="images/avatar.png" alt="Profile Picture">';
                } else {
                    echo '<img src="uploaded_img/'.$fetch['image'].'" alt="Profile Picture">';
                }
            ?>

            <div class="flex">
                <div class="input-roup">
                    <span class="span">Username: </span>
                    <input type="text" name="profile_name" value="<?php echo htmlspecialchars($fetch['name']); ?>" class="box" readonly>
                    <span>Email Address: </span>
                    <input type="email" name="profile_email" value="<?php echo htmlspecialchars($fetch['email']); ?>" class="box" readonly>
                    <span>Contact: </span>
                    <input type="text" name="profile_contact" value="<?php echo htmlspecialchars($fetch['contact']); ?>" class="box" readonly>
                </div>

                <div class="input-roup">
                    <span class="span">Postal Code: </span>
                    <input type="text" name="profile_postalCode" value="<?php echo htmlspecialchars($fetch['postalCode']); ?>" class="box" readonly>
                    <span class="span">House No: </span>
                    <input type="text" name="profile_houseNo" value="<?php echo htmlspecialchars($fetch['houseNo']); ?>" class="box" readonly>
                    <span>Street Name: </span>
                    <input type="text" name="profile_streetName" value="<?php echo htmlspecialchars($fetch['streetName']); ?>" class="box" readonly>
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


            <div class="">
                <button type="submit" name="editProfile" class="btn">Edit Profile</button>
                
                <button type="button" id="deleteAccountButton" class="btn" onclick="confirmDelete()">Delete Account</button>
                <button type="button" class="btn">Order History</button>
            </div>

           

           

            <div class="">
    <button type="button" id="match" class="btn" onclick="window.location.href='game.html'">Enjoy Maze</button>
</div>

<style>
    #match {
        width: 460px; 
    }
</style>

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

        function confirmDelete() {
            if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
                const userId = <?php echo json_encode($user_id); ?>;
                fetch('deleteAccount.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Your account has been deleted successfully.");
                        window.location.href = 'login.php';
                    } else {
                        alert("An error occurred: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred while deleting your account.");
                });
            }
        }

        document.getElementById("logoutButton").addEventListener("click", function() {
            window.location.href = "login.php";
        });
    </script>
   
</body>
</html>
