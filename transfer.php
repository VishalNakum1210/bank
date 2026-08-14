<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/functions.php";

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: auth/login.php");
    exit();
}

$id = $_SESSION['id'];
$sender_username = $_SESSION['username'];
$error_msg = "";
$error_color = "red";

// Handle Money Transfer Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['amount'])) {
    $target_username = trim($_POST['username']);
    $target_email = trim($_POST['email']);
    $amount = (float)$_POST['amount'];

    if ($amount <= 0) {
        $error_msg = "Please enter a valid transfer amount greater than 0.";
        $error_color = "red";
    } elseif (strtolower($target_username) === strtolower($sender_username)) {
        $error_msg = "You cannot transfer funds to your own account.";
        $error_color = "red";
    } else {
        // Validate sender balance
        $stmt_s = mysqli_prepare($conn, "SELECT amount FROM `money_bank` WHERE `account_id` = ?");
        mysqli_stmt_bind_param($stmt_s, "i", $id);
        mysqli_stmt_execute($stmt_s);
        $res_s = mysqli_stmt_get_result($stmt_s);

        if ($row_s = mysqli_fetch_assoc($res_s)) {
            $sender_balance = (float)$row_s['amount'];

            if ($sender_balance >= $amount) {
                // Find receiver by username
                $stmt_r = mysqli_prepare($conn, "SELECT account_id FROM `money_bank` WHERE `username` = ?");
                mysqli_stmt_bind_param($stmt_r, "s", $target_username);
                mysqli_stmt_execute($stmt_r);
                $res_r = mysqli_stmt_get_result($stmt_r);

                if ($row_r = mysqli_fetch_assoc($res_r)) {
                    $receiver_id = $row_r['account_id'];

                    // Validate receiver email matches
                    $stmt_re = mysqli_prepare($conn, "SELECT email FROM `persnol` WHERE `account_id` = ?");
                    mysqli_stmt_bind_param($stmt_re, "i", $receiver_id);
                    mysqli_stmt_execute($stmt_re);
                    $res_re = mysqli_stmt_get_result($stmt_re);

                    if ($row_re = mysqli_fetch_assoc($res_re)) {
                        if (strtolower(trim($row_re['email'])) === strtolower($target_email)) {
                            // Execute Transfer within DB Transaction
                            mysqli_begin_transaction($conn);
                            try {
                                // Deduct from sender
                                $new_sender_bal = $sender_balance - $amount;
                                $stmt_up_s = mysqli_prepare($conn, "UPDATE `money_bank` SET `amount` = ? WHERE `account_id` = ?");
                                mysqli_stmt_bind_param($stmt_up_s, "di", $new_sender_bal, $id);
                                mysqli_stmt_execute($stmt_up_s);
                                mysqli_stmt_close($stmt_up_s);

                                // Add to receiver
                                $stmt_up_r = mysqli_prepare($conn, "UPDATE `money_bank` SET `amount` = `amount` + ? WHERE `account_id` = ?");
                                mysqli_stmt_bind_param($stmt_up_r, "di", $amount, $receiver_id);
                                mysqli_stmt_execute($stmt_up_r);
                                mysqli_stmt_close($stmt_up_r);

                                // History for sender
                                $desc_s = "Money Sent to @" . $target_username;
                                $status_s = "Debited";
                                $stmt_h_s = mysqli_prepare($conn, "INSERT INTO `history` (`account_id`, `paymant_status`, `desc`, `time`, `amount`) VALUES (?, ?, ?, NOW(), ?)");
                                mysqli_stmt_bind_param($stmt_h_s, "issd", $id, $status_s, $desc_s, $amount);
                                mysqli_stmt_execute($stmt_h_s);
                                mysqli_stmt_close($stmt_h_s);

                                // History for receiver
                                $desc_r = "Money Received from @" . $sender_username;
                                $status_r = "Credited";
                                $stmt_h_r = mysqli_prepare($conn, "INSERT INTO `history` (`account_id`, `paymant_status`, `desc`, `time`, `amount`) VALUES (?, ?, ?, NOW(), ?)");
                                mysqli_stmt_bind_param($stmt_h_r, "issd", $receiver_id, $status_r, $desc_r, $amount);
                                mysqli_stmt_execute($stmt_h_r);
                                mysqli_stmt_close($stmt_h_r);

                                mysqli_commit($conn);
                                $error_msg = "Successfully transferred ₹" . format_inr($amount) . " to " . htmlspecialchars($target_username) . "!";
                                $error_color = "green";
                            } catch (Exception $e) {
                                mysqli_rollback($conn);
                                $error_msg = "Transfer failed due to a system error. Please try again.";
                                $error_color = "red";
                            }
                        } else {
                            $error_msg = "Receiver email address does not match account records.";
                            $error_color = "red";
                        }
                    } else {
                        $error_msg = "Receiver profile details not found.";
                        $error_color = "red";
                    }
                    mysqli_stmt_close($stmt_re);
                } else {
                    $error_msg = "Receiver username does not exist.";
                    $error_color = "red";
                }
                mysqli_stmt_close($stmt_r);
            } else {
                $error_msg = "Insufficient balance for this transfer.";
                $error_color = "red";
            }
        }
        mysqli_stmt_close($stmt_s);
    }
}

// Fetch sender account card details
$account_number = "XXXX XXXX XXXX XXXX";
$stmt_acc = mysqli_prepare($conn, "SELECT account_number FROM `account_details` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_acc, "i", $id);
mysqli_stmt_execute($stmt_acc);
$res_acc = mysqli_stmt_get_result($stmt_acc);
if ($row_acc = mysqli_fetch_assoc($res_acc)) {
    $account_number = $row_acc['account_number'];
}
mysqli_stmt_close($stmt_acc);

$account_holder = $sender_username;
$stmt_p = mysqli_prepare($conn, "SELECT name FROM `persnol` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_p, "i", $id);
mysqli_stmt_execute($stmt_p);
$res_p = mysqli_stmt_get_result($stmt_p);
if ($row_p = mysqli_fetch_assoc($res_p)) {
    $account_holder = $row_p['name'];
}
mysqli_stmt_close($stmt_p);

$money_bal = 0.0;
$stmt_m = mysqli_prepare($conn, "SELECT amount FROM `money_bank` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_m, "i", $id);
mysqli_stmt_execute($stmt_m);
$res_m = mysqli_stmt_get_result($stmt_m);
if ($row_m = mysqli_fetch_assoc($res_m)) {
    $money_bal = (float)$row_m['amount'];
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
    <link rel="stylesheet" href="assets/css/transfer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/side_nav.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money - NNP Bank</title>
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
                    <div class="amt-display">Balance<br>&#8377;<?php echo format_inr($money_bal); ?></div>
                    <img src="assets/images/logo.png" class="bank-logo" alt="Bank Logo">
                </div>
            </div>
        </div>

        <div class="input_line">
            <div class="title_line">
                <p>Transfer Money</p>
            </div>

            <form action="transfer.php" method="post">
                <div class="line1">
                    <span>
                        <p class="text">Receiver Username</p>
                        <input type="text" placeholder="Receiver Username" name="username" required autocomplete="off">
                    </span>
                </div>

                <div class="line1">
                    <span>
                        <p class="text">Receiver E-mail</p>
                        <input type="email" placeholder="Receiver E-mail" name="email" required autocomplete="off">
                    </span>
                </div>

                <div class="line1">
                    <span>
                        <p class="text">Amount (&#8377;)</p>
                        <input type="number" step="any" placeholder="Enter transfer amount" name="amount" required min="1">
                    </span>
                </div>

                <div class="btn-group">
                    <button type="submit" id="sub">Send Money</button>
                    <a href="index.php" class="back_link">
                        <button type="button" id="back">Back</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>