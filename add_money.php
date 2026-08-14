<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/config/internet_check.php";
require_once __DIR__ . "/includes/functions.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: auth/login.php");
    exit();
}

$username = $_SESSION['username'];
$id = $_SESSION['id'];
$error_msg = "";
$error_color = "red";

// Handle Add Money Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $new_amount = (float)$_POST['amount'];

    if ($new_amount > 0) {
        // Fetch current amount
        $stmt = mysqli_prepare($conn, "SELECT amount FROM `money_bank` WHERE `account_id` = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($res)) {
            $current_money = (float)$row['amount'];
            $updated_amount = $current_money + $new_amount;

            // Update balance
            $update_stmt = mysqli_prepare($conn, "UPDATE `money_bank` SET `amount` = ? WHERE `account_id` = ?");
            mysqli_stmt_bind_param($update_stmt, "di", $updated_amount, $id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);

            // Record transaction history
            $trans_desc = "Added Money (Deposit)";
            $status = "Credited";
            $hist_stmt = mysqli_prepare($conn, "INSERT INTO `history` (`account_id`, `paymant_status`, `desc`, `time`, `amount`) VALUES (?, ?, ?, NOW(), ?)");
            mysqli_stmt_bind_param($hist_stmt, "issd", $id, $status, $trans_desc, $new_amount);
            mysqli_stmt_execute($hist_stmt);
            mysqli_stmt_close($hist_stmt);

            $error_msg = "₹" . format_inr($new_amount) . " added to your account successfully!";
            $error_color = "green";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Please enter a valid positive amount.";
        $error_color = "red";
    }
}

// Fetch Account Info for Card preview
$account_number = "XXXX XXXX XXXX XXXX";
$stmt_acc = mysqli_prepare($conn, "SELECT account_number FROM `account_details` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_acc, "i", $id);
mysqli_stmt_execute($stmt_acc);
$res_acc = mysqli_stmt_get_result($stmt_acc);
if ($row_acc = mysqli_fetch_assoc($res_acc)) {
    $account_number = $row_acc['account_number'];
}
mysqli_stmt_close($stmt_acc);

$account_holder = $username;
$stmt_p = mysqli_prepare($conn, "SELECT name FROM `persnol` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_p, "i", $id);
mysqli_stmt_execute($stmt_p);
$res_p = mysqli_stmt_get_result($stmt_p);
if ($row_p = mysqli_fetch_assoc($res_p)) {
    $account_holder = $row_p['name'];
}
mysqli_stmt_close($stmt_p);

$money = 0.0;
$stmt_m = mysqli_prepare($conn, "SELECT amount FROM `money_bank` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_m, "i", $id);
mysqli_stmt_execute($stmt_m);
$res_m = mysqli_stmt_get_result($stmt_m);
if ($row_m = mysqli_fetch_assoc($res_m)) {
    $money = (float)$row_m['amount'];
}
mysqli_stmt_close($stmt_m);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/add_money.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/side_nav.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Money - NNP Bank</title>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>

    <?php
        if (!empty($error_msg)) {
            error_display($error_msg, $error_color);
        }
    ?>

    <div class="card1">
        <div class="card-column">
            <div class="card2">
                <div class="card-chip-row">
                    <img src="assets/images/chip.png" class="chip-lg" alt="Chip">
                    <img src="assets/images/visa.png" class="visa-lg" alt="Visa">
                </div>
                <div class="card-number"><?php echo htmlspecialchars($account_number); ?></div>
                <div class="card-name"><?php echo ucwords(htmlspecialchars($account_holder)); ?></div>
                <div class="card-footer">
                    <div class="amt-display">Balance<br>&#8377;<?php echo format_inr($money); ?></div>
                    <img src="assets/images/logo.png" class="bank-logo" alt="Bank Logo">
                </div>
            </div>
        </div>

        <div class="input_line">
            <div class="title_line">
                <p>Add Money</p>
            </div>

            <form action="add_money.php" method="post">
                <div class="line1">
                    <span>
                        <p class="text">Amount (&#8377;)</p>
                        <input type="number" step="any" placeholder="Enter amount to add" name="amount" required min="1">
                    </span>
                </div>

                <div class="btn-group">
                    <button type="submit" id="sub">Deposit Funds</button>
                    <a href="index.php" class="back_link">
                        <button type="button" id="back">Back</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>