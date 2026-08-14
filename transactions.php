<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/functions.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: auth/login.php");
    exit();
}

$username = $_SESSION['username'];
$id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/side_nav.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/transactions.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - NNP Bank</title>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>

    <div class="page-header">
        <h1 class="page-title">Transaction History</h1>
        <p class="page-subtitle">View all your recent debits, credits, and transfers in real-time.</p>
    </div>

    <div class="data-table">
        <div class="data-row data-header">
            <div>Status</div>
            <div>Description</div>
            <div>Date & Time</div>
            <div style="text-align: right;">Amount</div>
        </div>
        
        <?php
            $stmt = mysqli_prepare($conn, "SELECT paymant_status, `desc`, `time`, amount FROM `history` WHERE `account_id` = ? ORDER BY `time` DESC");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $selected_result = mysqli_stmt_get_result($stmt);

            if ($selected_result && mysqli_num_rows($selected_result) > 0) {
                while ($row = mysqli_fetch_assoc($selected_result)) {
                    $is_credit = ($row["paymant_status"] === "Credited");
                    $status_class = $is_credit ? "status-credited" : "status-debited";
                    $sign = $is_credit ? "+" : "-";
                    $color_style = $is_credit ? "color: #10b981;" : "color: #ef4444;";
                    
                    echo '
                    <div class="data-row">
                        <div class="data-cell">
                            <span class="status-badge ' . $status_class . '">' . htmlspecialchars($row["paymant_status"]) . '</span>
                        </div>
                        <div class="data-cell">
                            ' . htmlspecialchars($row["desc"]) . '
                        </div>
                        <div class="data-cell">
                            ' . date('M d, Y h:i A', strtotime($row["time"])) . '
                        </div>
                        <div class="data-cell" style="text-align: right; ' . $color_style . ' font-weight: 600;">
                            ' . $sign . ' &#8377;' . format_inr($row["amount"]) . '
                        </div>
                    </div>';
                }
            } else {
                echo "<div class='data-row' style='grid-template-columns: 1fr; text-align: center; color: #64748b; padding: 40px;'>No transactions recorded yet.</div>";
            }
            mysqli_stmt_close($stmt);
        ?>
    </div>
</body>
</html>