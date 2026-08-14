<?php
    session_start();
    include "templets/_dbconnect.php";
    include "templets/internet_connection.php";
    // include "email/test.php";
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
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style/add.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style/side_nav.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Money - NNP Bank</title>

    <style>
        .card2{
            background: url('pic/bg6.jpg') center/cover, #000 !important;
            padding: 30px;
            box-sizing: border-box;
            justify-content: space-between;
            position: relative;
            top: 0;
            overflow: hidden;
            flex-shrink: 0;
        }
        .card1:hover .card2 {
            box-shadow: 0 25px 50px rgba(0,0,0,0.6);
        }
        .card2::before {
            content: ''; position: absolute; top:0; left:0; right:0; bottom:0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
        }
        .card-chip-row { display: flex; justify-content: space-between; position: relative; z-index: 2; }
        .chip-lg { height: 45px; }
        .visa-lg { height: 28px; filter: brightness(0) invert(1); }
        .card-number { font-size: 26px; font-weight: 700; letter-spacing: 4px; position: relative; z-index: 2; text-align: center; text-shadow: 0 4px 8px rgba(0,0,0,0.6); color: white; margin: 0;}
        .card-name { font-size: 20px; font-weight: 600; text-transform: uppercase; position: relative; z-index: 2; letter-spacing: 1px; color: white; margin: 0;}
        .card-footer { display: flex; justify-content: space-between; align-items: flex-end; position: relative; z-index: 2; color: white; margin: 0;}
        .bank-logo { height: 50px; }
        .amt-display { font-size: 13px; font-weight: 600; text-transform: uppercase;}
    </style>

</head>
<body>
    <?php include "templets/side_nav.php"; ?>

    <?php

        function error_display($error, $color = "red"){
            $bg_color = ($color == "green" || $color == "#16a34a" || $color == "#10b981") ? "#16a34a" : (($color == "red" || $color == "#e11d48") ? "#e11d48" : $color);
            $icon = ($bg_color == "#16a34a") ? "bx-check-circle" : "bx-error";
            echo '<div style="background-color: '.$bg_color.';" class="error_noti">
                <i class="bx '.$icon.'"></i>
                <span>
                    '.$error.'  
                </span>
                <i class="bx bx-x icon2" onclick="this.parentElement.remove();"></i>
            </div>';
        }

        if(isset($_POST['amount'])){

            #send email (commented out)
            /*
            function send_mail($amount){
                $user_email = $_SESSION['email'];
                $title = "Credited Money";
                $desc = "Your A/C can be Credited with Rs".$amount;

                mail_sender( $user_email, $title, $desc);
            }
            */

            #check
            function check($new_amount){
                if($new_amount > 0){
                    add($new_amount);
                }
                else{
                    $error =  "invalid amount";
                    error_display($error, "red");
                }
            }

            #add money
            function add($new_amount){
                global $conn, $id;
                $store_money = "SELECT * FROM `money_bank` WHERE `account_id` = '$id';";
                $result_money = mysqli_query($conn, $store_money);
                $money_row = mysqli_num_rows($result_money);

                if($money_row == 1){
                    while($row = mysqli_fetch_assoc($result_money)){
                        $money = $row['amount'];
                    }
                }
                $add_amount = $money + $new_amount;
                $add_money_sql = "UPDATE `money_bank` SET `amount`='$add_amount' WHERE `account_id` = '$id';";
                $result_add_money = mysqli_query($conn, $add_money_sql);
                // send_mail($new_amount);

                #entey in history
                $date = date('Y-m-d H:i:s');
                $entery = "INSERT INTO `history`(`account_id`, `paymant_status`, `desc`, `time`, `amount`) VALUES ('$id','Credited','Added Money','$date','$new_amount');";
                $result_entery = mysqli_query($conn, $entery);

                $error = "Money added to your account successfully";
                error_display($error, "green");
            }

            $new_amount = $_POST['amount'];
            check($new_amount);
        }

    ?>

    
    <div class="card1">
        <div class="card2">
        <?php
            #to store account_number
            $store_account_number = "SELECT * FROM `account_details` WHERE `account_id` = '$id';";
            $result_account_number = mysqli_query($conn, $store_account_number);
            $account_number_row = mysqli_num_rows($result_account_number);

            if($account_number_row == 1){
                while($row = mysqli_fetch_assoc($result_account_number)){
                    $account_number = $row['account_number'];
                }
            }

            #to store account_holder_name 
            $store_account_holder = "SELECT * FROM `persnol` WHERE `account_id` = '$id';";
            $result_account_holder = mysqli_query($conn, $store_account_holder);
            $account_holder_row = mysqli_num_rows($result_account_holder);

            if($account_holder_row == 1){
                while($row = mysqli_fetch_assoc($result_account_holder)){
                    $account_holder = $row['name'];
                }
            }

            #to store money
            $store_money = "SELECT * FROM `money_bank` WHERE `account_id` = '$id';";
            $result_money = mysqli_query($conn, $store_money);
            $money_row = mysqli_num_rows($result_money);

            if($money_row == 1){
                while($row = mysqli_fetch_assoc($result_money)){
                    $money = $row['amount'];
                }
            }

        ?>
            <div class="card-chip-row">
                <img src="pic/chip.png" class="chip-lg">
                <img src="pic/visa.png" class="visa-lg">
            </div>
            <div class="card-number"><?php echo htmlspecialchars($account_number ?? ''); ?></div>
            <div class="card-name"><?php echo ucwords(htmlspecialchars($account_holder ?? '')); ?></div>
            <div class="card-footer">
                <div class="amt-display">Balance<br>&#8377;<?php echo number_format((float)($money ?? 0), 2); ?></div>
                <img src="pic/logo.png" class="bank-logo">
            </div>
        </div>

        <div id="line_input">
            <form action="add_money.php" method="post">
                <span>
                    <p class="text">Amount</p>
                    <input type="number" name="amount" required>
                </span>
                <br>
                <button id="sub">submit</button>
            </form>
            
            <a href="index.php">
                <button id="back">back</button>
            </a>
        </div>
    </div>
</body>
</html>