<?php
    include 'config.php';
    session_start();

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $select = mysqli_query($conn, "SELECT * FROM `admin` WHERE adminID = '$user_id'") or die('Query failed');

        if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
        } else {
            $fetch = null;
        }
    } else {
        $fetch = null;
    }
?>

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
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'includes/adminNavbar.php' ?>
<div class="profile">
    <form action="EditProfile.php" method="get">
        <h1><?php echo $fetch ? htmlspecialchars($fetch['name']) : 'No admin found'; ?> Profile </h1>
        <div class="flex">
            <div class="input-roup">
                <span class="span">Name: </span>
                <input type="text" name="profile_name" value="<?php echo $fetch ? htmlspecialchars($fetch['name']) : 'No admin found'; ?>" class="box" readonly style="width:200px;">

                <span>Email: </span>
                <input type="email" name="profile_email" value="<?php echo $fetch ? htmlspecialchars($fetch['email']) : 'No admin found'; ?>" class="box" readonly style="width: 200px;">

                <span>Password: </span>
                <input type="password" id="profile_password" name="profile_password" value="<?php echo $fetch ? htmlspecialchars($fetch['password']) : ''; ?>" class="box" readonly style="width: 200px" class="fas fa-eye eye-icon" id="togglePassword" onclick="togglePassword()">
                <i ></i>
            </div>
        </div>
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
</script>

</body>
</html>
