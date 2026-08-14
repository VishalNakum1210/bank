<?php
/**
 * Dashboard & Account Overview
 * NNP Online Banking System
 */
require_once __DIR__ . "/config/internet_check.php";
check_internet();

session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/functions.php";

// Authentication Guard
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: auth/login.php");
    exit();
}

$id = $_SESSION['id'];
$username = $_SESSION['username'];
$error_msg = "";
$error_color = "red";

// Handle Debit Card Order Request
if (isset($_POST['order_done'])) {
    $stmt = mysqli_prepare($conn, "SELECT amount FROM `money_bank` WHERE `account_id` = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        if ($row['amount'] >= 399) {
            $new_bal = $row['amount'] - 399;
            
            // Deduct balance and activate debit card
            $update_stmt = mysqli_prepare($conn, "UPDATE `money_bank` SET `amount` = ?, `debit_card` = 1 WHERE `account_id` = ?");
            mysqli_stmt_bind_param($update_stmt, "di", $new_bal, $id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);

            // Record transaction history
            $trans_desc = "Purchase VISA Platinum Debit Card";
            $status = "Debited";
            $card_cost = 399;
            $hist_stmt = mysqli_prepare($conn, "INSERT INTO `history` (`account_id`, `paymant_status`, `desc`, `time`, `amount`) VALUES (?, ?, ?, NOW(), ?)");
            mysqli_stmt_bind_param($hist_stmt, "issd", $id, $status, $trans_desc, $card_cost);
            mysqli_stmt_execute($hist_stmt);
            mysqli_stmt_close($hist_stmt);

            $error_msg = "Debit Card Ordered & Activated Successfully!";
            $error_color = "green";
        } else {
            $error_msg = "Insufficient funds in your account to order a debit card (₹399 required).";
            $error_color = "red";
        }
    }
    mysqli_stmt_close($stmt);
}

// Fetch current account balance & debit card status
$total_amount = 0.0;
$debit_card_status = 0;
$stmt_mb = mysqli_prepare($conn, "SELECT amount, debit_card FROM `money_bank` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_mb, "i", $id);
mysqli_stmt_execute($stmt_mb);
$res_mb = mysqli_stmt_get_result($stmt_mb);
if ($row_mb = mysqli_fetch_assoc($res_mb)) {
    $total_amount = (float)$row_mb['amount'];
    $debit_card_status = (int)($row_mb['debit_card'] ?? 0);
}
mysqli_stmt_close($stmt_mb);

// Fetch account details
$account_number = "XXXX XXXX XXXX XXXX";
$stmt_acc = mysqli_prepare($conn, "SELECT account_number FROM `account_details` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_acc, "i", $id);
mysqli_stmt_execute($stmt_acc);
$res_acc = mysqli_stmt_get_result($stmt_acc);
if ($row_acc = mysqli_fetch_assoc($res_acc)) {
    $account_number = $row_acc['account_number'];
}
mysqli_stmt_close($stmt_acc);

// Fetch personal info
$holder_name = $username;
$holder_email = $_SESSION['email'] ?? "";
$stmt_p = mysqli_prepare($conn, "SELECT name, email FROM `persnol` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_p, "i", $id);
mysqli_stmt_execute($stmt_p);
$res_p = mysqli_stmt_get_result($stmt_p);
if ($row_p = mysqli_fetch_assoc($res_p)) {
    $holder_name = $row_p['name'];
    $holder_email = $row_p['email'];
}
mysqli_stmt_close($stmt_p);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/side_nav.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NNP Bank</title>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>

    <?php
        if (!empty($error_msg)) {
            error_display($error_msg, $error_color);
        }
    ?>

    <div class="dashboard-container">
        
        <!-- LEFT COLUMN: MOBILE PHONE APP SIMULATOR (100vh fitted) -->
        <div class="mobile_fram">
            <div class="mobile_display">
                <!-- Phone Status Bar -->
                <div class="land">
                    <span class="date">
                        <?php echo date('D, M d'); ?>
                    </span>
                    <img src="assets/images/symbol-removebg-preview.png" id="symbol" alt="Network">
                </div>

                <!-- Mini Card inside Phone -->
                <div class="card">
                    <div class="first_line">
                        <span id="bank_name">NNP Bank</span>
                        <img src="assets/images/visa.png" id="visa" alt="Visa">
                    </div>

                    <div class="amount_details">
                        Balance
                        <div>&#8377;<span id="total_amount"><?php echo format_inr($total_amount); ?></span></div>
                    </div>

                    <div class="last_line">
                        <span id="user_name"><?php echo htmlspecialchars($username); ?></span>
                        <img src="assets/images/chip.png" id="chip" alt="Chip">
                    </div>
                </div>

                <!-- Recent Transactions in Phone -->
                <div class="header">
                    Recent Transactions
                </div>

                <div class="history">
                    <?php
                    $hist_stmt = mysqli_prepare($conn, "SELECT paymant_status, `desc`, time, amount FROM `history` WHERE `account_id` = ? ORDER BY time DESC LIMIT 6");
                    mysqli_stmt_bind_param($hist_stmt, "i", $id);
                    mysqli_stmt_execute($hist_stmt);
                    $result_history = mysqli_stmt_get_result($hist_stmt);

                    if ($result_history && mysqli_num_rows($result_history) > 0) {
                        while ($row = mysqli_fetch_assoc($result_history)) {
                            $is_credit = ($row['paymant_status'] == 'Credited');
                            $sign = $is_credit ? "+" : "-";
                            $color = $is_credit ? "#10b981" : "#ef4444";
                            
                            echo '
                                <div class="history_line">
                                    <div class="history_left">
                                        <img src="assets/images/success.png" id="suc" alt="Status">
                                        <span id="payment_status">' . htmlspecialchars($row['paymant_status']) . '</span>
                                    </div>
                                    <span id="amount" style="color: ' . $color . ';">' . $sign . ' &#8377;' . format_inr($row['amount']) . '</span>
                                </div>                            
                            ';
                        }
                    } else {
                        echo '<div style="color: #64748b; text-align: center; font-size: 13px; margin-top: 30px;">No recent transactions</div>';
                    }
                    mysqli_stmt_close($hist_stmt);
                    ?>
                </div>

                <!-- Phone Bottom Home Bar -->
                <div class="back"></div>
            </div>
        </div>

        <!-- RIGHT COLUMN: MAIN DASHBOARD PANELS & DEBIT CARD SHOWCASE -->
        <div class="right-panel">
            
            <!-- Welcome Header Card -->
            <div class="user-welcome-card">
                <div class="welcome-info">
                    <h2>Welcome back, <?php echo ucwords(htmlspecialchars($holder_name)); ?>!</h2>
                    <p>Manage your accounts, cards, and daily transactions seamlessly.</p>
                </div>
                <div class="user-quick-stats">
                    <div class="quick-stat-badge">
                        <span class="quick-stat-label">Available Balance</span>
                        <span class="quick-stat-val" style="color: #10b981;">&#8377;<?php echo format_inr($total_amount); ?></span>
                    </div>
                    <div class="quick-stat-badge">
                        <span class="quick-stat-label">Account Status</span>
                        <span class="quick-stat-val" style="color: #38bdf8;">Verified Active</span>
                    </div>
                </div>
            </div>

            <!-- Debit Card Showcase & Order Section -->
            <div class="order_page">
                
                <!-- Physical ATM Card Preview -->
                <div class="card2">
                    <div class="pic_line">
                        <img class="pic" src="assets/images/chip.png" alt="Chip">
                        <img src="assets/images/visa.png" alt="Visa">
                    </div>

                    <div class="number_line">
                        <p id="number"><?php echo htmlspecialchars($account_number); ?></p>
                        <img class="wifi-icon" src="assets/images/wifi.png" alt="NFC">
                    </div>

                    <div class="name_line">
                        <p><?php echo ucwords(htmlspecialchars($holder_name)); ?></p>
                    </div>

                    <div class="last">
                        <div class="valid_box">
                            <div>Valid Thru</div>
                            <span>12/28</span>
                        </div>
                        <img src="assets/images/logo.png" class="logo" alt="Bank Logo">
                    </div>
                </div>

                <!-- Order Action / Status Column -->
                <div class="order-info-col">
                    <?php if ($debit_card_status == 1): ?>
                        <div class="already-ordered-box">
                            <img src="assets/images/success.png" alt="Success">
                            <div class="already-text">
                                <h3>Debit Card Active</h3>
                                <p>Your contactless VISA Platinum Debit Card is currently active and linked to your account.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="sen">
                            Order Your Official VISA Platinum Debit Card
                        </div>
                        <div class="price-tag">
                            Special Price: <del id="cut_number">&#8377;499</del> <span class="special-price">&#8377;399 Only</span>
                        </div>
                        <form action="index.php" method="post">
                            <button type="submit" name="order_done" id="order_button">Let's Order Now</button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Bottom Personal Account Details Bar -->
            <div class="not">
                <div class="detail">
                    <span class="text_name">Account Holder:</span>
                    <span class="holder_name"><?php echo ucwords(htmlspecialchars($holder_name)); ?></span>
                </div>
                <div class="detail">
                    <span class="text_email">Registered Email:</span>
                    <span class="holder_email"><?php echo htmlspecialchars($holder_email); ?></span>
                </div>
                <div class="detail">
                    <span class="text_name">Account No:</span>
                    <span class="holder_name" style="font-family: monospace; letter-spacing: 1px;"><?php echo htmlspecialchars($account_number); ?></span>
                </div>
            </div>

        </div>

    </div>
</body>
</html>
