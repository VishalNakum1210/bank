<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/functions.php";

$error_condition = 0;
$notification_msg = "";
$notification_color = "red";

// Step 1: Search for username & generate OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && !isset($_POST['otp'])) {
    $username = trim($_POST['username']);
    
    $stmt = mysqli_prepare($conn, "SELECT account_id FROM `money_bank` WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        $acc_id = $row['account_id'];
        $_SESSION['temp_id'] = $acc_id;

        // Generate OTP
        $otp = rand(1000, 9999);
        $_SESSION['otp'] = $otp;

        $notification_msg = "OTP generated: " . $otp . " (Demo Mode: Use this OTP to reset your password)";
        $notification_color = "green";
    } else {
        $notification_msg = "Username not found.";
        $notification_color = "red";
        $error_condition = 1;
    }
    mysqli_stmt_close($stmt);
}

// Step 2: Validate OTP and update password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp']) && isset($_POST['password'])) {
    $entered_otp = trim($_POST['otp']);
    $new_password = $_POST['password'];

    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $entered_otp) {
        $acc_id = $_SESSION['temp_id'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "UPDATE `money_bank` SET `user_password` = ? WHERE `account_id` = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $acc_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Clear temporary reset session
        unset($_SESSION['otp']);
        unset($_SESSION['temp_id']);

        header("Location: login.php?reset=success");
        exit();
    } else {
        $notification_msg = "Incorrect OTP entered.";
        $notification_color = "red";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="shortcut icon" href="../assets/images/logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/forgot_password.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - NNP Bank</title>
</head>
<body>

    <?php
        if (!empty($notification_msg)) {
            error_display($notification_msg, $notification_color);
        }
    ?>

    <form action="forgot_password.php" method="post">
        <div class="card">
            <p class="header">Forgot Password</p>

            <?php if (!isset($_SESSION['otp']) || $error_condition == 1): ?>
                <span class="line">
                    <p id="user_name" class="header_text">Username</p>
                    <input type="text" name="username" required autocomplete="username">
                </span>
            <?php else: ?>
                <span class="line">
                    <p id="otp" class="header_text">OTP</p>
                    <input type="number" name="otp" required placeholder="Enter 4-digit OTP">
                </span>

                <span class="line">
                    <p id="new_pass" class="header_text">New Password</p>
                    <input type="password" name="password" required autocomplete="new-password" placeholder="Enter new password">
                </span>
            <?php endif; ?>

            <span class="line">
                <button type="submit" name="submit" id="sub">Submit</button>
            </span>

            <div style="text-align: center; margin-top: 15px;">
                <a href="login.php" style="color: #64748b; font-size: 14px; text-decoration: none;">&larr; Back to Login</a>
            </div>
        </div>
    </form>
</body>
</html>
