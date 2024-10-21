
<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Connect to the database
    $conn = mysqli_connect("localhost", "root", "", "woodlak", "3306");

    // Get user ID or email from session (assuming user_id is stored in session)
    $user_id = $_SESSION['user_id'];

    // Fetch customer details from the 'customer' table
    $query = "SELECT name, email, contact, houseNo, streetName, city, postalCode FROM customer WHERE customerID = '$user_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Extract customer details
        $name = $row['name'];
        $email = $row['email'];
        $contact = $row['contact'];
        $houseNo = $row['houseNo'];
        $streetName = $row['streetName'];
        $city = $row['city'];
        $postalCode = $row['postalCode'];
    }
} else {
    // If the user is not logged in, set variables to empty
    $name = $email = $contact = $houseNo = $streetName = $city = $postalCode = "";
}
?>


<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Shipping details</title>
<link rel="stylesheet" type="text/css" href="style_orderDetails.css">
<link rel="stylesheet" href="../home.css">
<script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

   <?php include '../../includes/navbar.php'; ?>

    <section class="banner">
        <h2 class="text-3xl">SHIPPING DETAILS</h2>
        <div class="card-container">
            <div class="card-img">
              
            </div>

            <div class="card-content">
                <form method="POST" action="process_shipping.php">
                <div class="form-row">
                        <input type="text" name="name" placeholder="Name" id="name" value="<?php echo $name; ?>" required>
                        <input type="text" name="phone-Num" placeholder="Phone Number" id="phone-Num" value="<?php echo $contact; ?>" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="Address-one" placeholder="House No" id="Address-one" value="<?php echo $houseNo; ?>">
                        <input type="text" name="Address-Two" placeholder="Street Name" id="Address-Two" value="<?php echo $streetName; ?>" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="Address-three" placeholder="City" id="Address-three" value="<?php echo $city; ?>" required>
                        <input type="text" name="Address-four" placeholder="Postal Code" id="Address-four" value="<?php echo $postalCode; ?>" required>
                    </div>
                    <div class="form-row">
                        <input type="email" name="Email" placeholder="Email" id="Email" value="<?php echo $email; ?>"  required >
                    </div>
                    <div class="form-row">
                        <input type="submit" value="SUBMIT!" onclick="return validateAll();">
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
    function validateAll() {
        var name = document.getElementById("name").value;
        var phoneNumber = document.getElementById("phone-Num").value;
        var AddressOne = document.getElementById("Address-one").value;
        var AddressTwo = document.getElementById("Address-Two").value;
        var AddressThree = document.getElementById("Address-three").value;
        var AddressFour = document.getElementById("Address-four").value;

        if (name === "" || phoneNumber === "") {
            alert("Name and phone number are required fields.");
            return false;
        }
        if (AddressThree === "" || AddressTwo === "" || AddressFour === "") {
            alert("Full Address is required field.");
            return false;
        }

        if (isNaN(phoneNumber)) {
            alert("Phone number must be a numeric value.");
            return false;
        }
        if (AddressOne !== "" && isNaN(AddressOne)) {
            alert("House No must be a numeric value.");
            return false;
        }

        if (isNaN(AddressFour)) {
            alert("Postal code must be a numeric value.");
            return false;
        }

        alert("Shipping details successfully added");
        return true;
    }

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
