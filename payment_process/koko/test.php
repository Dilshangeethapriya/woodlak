<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS</title>
</head>
<body>
    <h1>Send SMS via Infobip</h1>
    <form action="send_sms.php" method="POST">
        <label for="mobileNumber">Enter Mobile Number:</label><br><br>
        <input type="text" id="mobileNumber" name="mobileNumber" required><br><br>
        <input type="submit" value="Send SMS">
    </form>
</body>
</html>
