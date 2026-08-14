<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/functions.php";

// If already logged in, redirect to dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: ../index.php");
    exit();
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = mysqli_prepare($conn, "SELECT account_id, username, user_password FROM `money_bank` WHERE `username` = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row['user_password'])) {
                    $_SESSION['login'] = true;
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['id'] = $row['account_id'];

                    // Fetch user's email
                    $acc_id = $row['account_id'];
                    $email_stmt = mysqli_prepare($conn, "SELECT email FROM `persnol` WHERE `account_id` = ?");
                    if ($email_stmt) {
                        mysqli_stmt_bind_param($email_stmt, "i", $acc_id);
                        mysqli_stmt_execute($email_stmt);
                        $res_email = mysqli_stmt_get_result($email_stmt);
                        if ($email_row = mysqli_fetch_assoc($res_email)) {
                            $_SESSION['email'] = $email_row['email'];
                        }
                        mysqli_stmt_close($email_stmt);
                    }

                    header("Location: ../index.php");
                    exit();
                } else {
                    $error_msg = "Invalid password. Please try again.";
                }
            } else {
                $error_msg = "Username does not exist.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_msg = "Database query error.";
        }
    } else {
        $error_msg = "Please fill in all fields.";
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
    <link rel="stylesheet" href="../assets/css/login.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NNP Bank</title>
</head>
<body>

    <?php
        if (!empty($error_msg)) {
            error_display($error_msg, "red");
        }
    ?>

    <div class="card">
        <form action="login.php" method="post">
            <div class="line_title">
                <p>Login</p>
            </div>

            <div class="line">
                <p class="text">Username</p>
                <input type="text" name="username" required autocomplete="username">
            </div>

            <div class="line">
                <p class="text">Password</p>
                <input type="password" name="password" required autocomplete="current-password">
                <a href="forgot_password.php"><p class="forget">Forgot password?</p></a>
            </div>

            <button id="sub" type="submit">Submit</button>
        </form>
        
        <button id="back" onclick="window.history.back();">Back</button>

        <div class="registra">
            <p>Don't have an account? <a href="register.php">Click here</a></p>
        </div>
    </div>
</body>
</html>
