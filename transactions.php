<?php
    session_start();
    include "templets/_dbconnect.php";
    if(!isset($_SESSION["login"])){
        header("location: login.php");
    }
    else{
        $username = $_SESSION['username'];
        $id = $_SESSION['id'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="pic/logo.png">
    <link rel="shortcut icon" href="pic/logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style/side_nav.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style/transactions.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - NNP Bank</title>
</head>
<body>
   <?php
        include "templets/side_nav.php";
   ?>

    <div class="page-header">
        <h1 class="page-title">Transaction History</h1>
        <p class="page-subtitle">View all your recent account activity.</p>
    </div>

    <div class="data-table">
        <div class="data-row data-header">
            <div>Status</div>
            <div>Description</div>
            <div>Date & Time</div>
            <div style="text-align: right;">Amount</div>
        </div>
        
        <?php
            $select_history = "SELECT * FROM `history` WHERE `account_id` = '$id' ORDER BY time DESC;";
            $selected_result = mysqli_query($conn, $select_history);
            $select_row = mysqli_num_rows($selected_result);

            if($select_row > 0){
                while($row = mysqli_fetch_assoc($selected_result)){
                    $is_credit = $row["paymant_status"] == "Credited";
                    $status_class = $is_credit ? "status-credited" : "status-debited";
                    $sign = $is_credit ? "+" : "-";
                    $color_style = $is_credit ? "color: green;" : "color: red;";
                    
                    echo '
                    <div class="data-row">
                        <div class="data-cell">
                            <span class="status-badge '.$status_class.'">'.$row["paymant_status"].'</span>
                        </div>
                        <div class="data-cell">
                            '.htmlspecialchars($row["desc"]).'
                        </div>
                        <div class="data-cell">
                            '.date('M d, Y h:i A', strtotime($row["time"])).'
                        </div>
                        <div class="data-cell" style="text-align: right; '.$color_style.'">
                            '.$sign.' &#8377;'.number_format($row["amount"], 2).'
                        </div>
                    </div>';
                }
            } else {
                echo "<div class='data-row' style='grid-template-columns: 1fr; text-align: center; color: #64748b; padding: 40px;'>No transactions found.</div>";
            }
        ?>
    </div>
</body>
</html>