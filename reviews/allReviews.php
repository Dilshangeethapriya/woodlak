<?php
include '../config/dbconnect.php'; 

// Get the product ID
$productID = intval($_REQUEST["PRODUCTC"]); 

// Fetch all reviews for this product
$reviewsQuery = $conn->prepare("SELECT * FROM review WHERE productID = ? ORDER BY createdAt DESC");
$reviewsQuery->bind_param('i', $productID);
$reviewsQuery->execute();
$reviews = $reviewsQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reviews</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100" style=" background-image: url('../resources/images/bg2.png');background-repeat: no-repeat; background-attachment: fixed; background-size: cover;">

    <!-- Navbar -->
    <nav class="bg-[#78350f] text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-xl font-semibold">WOODLAK</a>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-gray-200">Home</a></li>
                <li><a href="shop.php" class="hover:text-gray-200">Shop</a></li>
                <li><a href="contact.php" class="hover:text-gray-200">Contact</a></li>
                <li><a href="about.php" class="hover:text-gray-200">About Us</a></li>
                <!-- Conditionally show login/logout buttons -->
                <?php if (isset($_SESSION['user_name'])): ?>
                    <li><a href="profile.php" class="hover:text-gray-200">Profile</a></li>
                    <li><a href="logout.php" class="hover:text-gray-200">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="hover:text-gray-200">Login</a></li>
                    <li><a href="register.php" class="hover:text-gray-200">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto my-10">
        <div class="review-section max-w-screen-lg mx-auto rounded-md p-5" style="background-color: rgba(220, 255, 220, 0.7);">
            <h2 class="text-[#C4A484] text-4xl mb-4 text-center">All Product Reviews</h2>

            <!-- Display all reviews -->
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="review bg-[#1f2937] p-4 rounded-lg mb-4">
                    <h4 class="text-[#C4A484] text-lg"><b><?php echo htmlspecialchars($review['customerName']); ?></b> 
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-300 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                        </svg>
                        <p class="ms-2 text-sm font-bold text-gray-900 dark:text-white"><?php echo $review['rating']; ?></p>
                    </div>
                    </h4>
                    <p class="text-black"><?php echo htmlspecialchars($review['reviewText']); ?></p>
                    <p><small class="text-gray-500"><?php echo $review['createdAt']; ?></small></p>
                    <hr class="my-4">
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#78350f] text-white py-6">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 WOODLAK. All rights reserved.</p>
            <p><a href="privacy.php" class="hover:underline">Privacy Policy</a> | <a href="terms.php" class="hover:underline">Terms of Service</a></p>
        </div>
    </footer>

</body>
</html>
