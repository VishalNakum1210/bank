<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/functions.php";

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile_number'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $account_type = trim($_POST['account_type'] ?? 'Savings');
    $pin = trim($_POST['pin'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($mobile) || empty($mail) || empty($username) || empty($password) || empty($pin)) {
        $error_msg = "Please fill in all required fields.";
    } elseif ($age < 18) {
        $error_msg = "You are not eligible to open an account (must be 18 or older).";
    } else {
        // Check username uniqueness
        $stmt = mysqli_prepare($conn, "SELECT account_id FROM `money_bank` WHERE `username` = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res_user = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($res_user) > 0) {
            $error_msg = "Username is already taken by another user.";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);

            // Check email uniqueness
            $stmt = mysqli_prepare($conn, "SELECT account_id FROM `persnol` WHERE `email` = ?");
            mysqli_stmt_bind_param($stmt, "s", $mail);
            mysqli_stmt_execute($stmt);
            $res_email = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($res_email) > 0) {
                $error_msg = "Email is already in use.";
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);

                // Insert into persnol
                $stmt_persnol = mysqli_prepare($conn, "INSERT INTO `persnol` (`name`, `mobile_number`, `email`) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt_persnol, "sss", $name, $mobile, $mail);
                mysqli_stmt_execute($stmt_persnol);
                mysqli_stmt_close($stmt_persnol);

                // Generate random account number
                $rand1 = rand(8000, 9999);
                $rand2 = rand(1000, 9999);
                $rand3 = rand(1000, 9999);
                $rand4 = rand(1000, 9999);
                $account_number = "$rand1 $rand2 $rand3 $rand4";

                // Insert into account_details
                $bank_title = "NNP Bank";
                $stmt_acc = mysqli_prepare($conn, "INSERT INTO `account_details` (`bank_name`, `account_number`, `account_type`, `age`, `pin`) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt_acc, "sssis", $bank_title, $account_number, $account_type, $age, $pin);
                mysqli_stmt_execute($stmt_acc);
                mysqli_stmt_close($stmt_acc);

                // Insert into money_bank
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $initial_balance = 0;
                $stmt_mb = mysqli_prepare($conn, "INSERT INTO `money_bank` (`username`, `user_password`, `amount`, `debit_card`) VALUES (?, ?, ?, 0)");
                mysqli_stmt_bind_param($stmt_mb, "ssi", $username, $hashed_password, $initial_balance);
                mysqli_stmt_execute($stmt_mb);
                $new_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt_mb);

                // Set session
                $_SESSION['login'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $mail;
                $_SESSION['id'] = $new_id;

                header("Location: ../index.php");
                exit();
            }
        }
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
    <link rel="stylesheet" href="../assets/css/register.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - NNP Bank</title>
</head>
<body>

    <?php
        if (!empty($error_msg)) {
            error_display($error_msg, "red");
        }
    ?>

    <div class="card">
        <div class="title_line">
            <p>Registration</p>
        </div>

        <form action="register.php" method="post">
            <div class="line">
                <span>
                    <p class="text">Full Name</p>
                    <input type="text" name="name" required placeholder="John Doe">
                </span>
    
                <span>
                    <p class="text">Mobile Number</p>
                    <input type="number" name="mobile_number" required placeholder="9876543210">
                </span>
    
                <span>
                    <p class="text">E-mail</p>
                    <input type="email" name="mail" required placeholder="john@example.com">
                </span>
            </div>
    
            <div class="line">
                <span>
                    <p class="text">Age</p>
                    <input type="number" name="age" required min="18" placeholder="18+">
                </span>
    
                <span>
                    <p class="text">Account Type</p>
                    <input type="text" name="account_type" required value="Savings">
                </span>
    
                <span>
                    <p class="text">4-Digit PIN</p>
                    <input type="password" name="pin" required maxlength="4" placeholder="****">
                </span>
            </div>
    
            <div class="line">
                <span>
                    <p class="text">Username</p>
                    <input type="text" name="username" required autocomplete="username">
                </span>
    
                <span>
                    <p class="text">Password</p>
                    <input type="password" name="password" required autocomplete="new-password">
                </span>
            </div>
    
            <button id="sub" type="submit">Submit</button>
        </form>

        <a href="login.php">
            <button id="back">Back to Login</button>
        </a>
    </div>
</body>
</html>
