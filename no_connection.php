<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/no_connection.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - NNP Bank</title>
</head>
<body>
    <img src="assets/images/no1.jpg" id="img_no_conn" alt="Offline Indicator">
    <p class="text">You are currently offline. Please check your internet connection.</p>
    <ul class="text" style="list-style-type: disc; padding-left: 20px;">
        <li>Check your network cables, modem, and router</li>
        <li>Reconnect to Wi-Fi or cellular network</li>
    </ul>

    <br>
    <br>

    <a href="index.php"><button id="re">Retry Connection</button></a>
</body>
</html>