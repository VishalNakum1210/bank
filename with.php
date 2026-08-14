<?php
    session_start();
    include "templets/_dbconnect.php";
    // include "email/test.php";
    if(!isset($_SESSION['login'])){
        header("location: login.php");
    }
    else{
        $id = $_SESSION['id'];
        $username = $_SESSION['username'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style/with.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style/side_nav.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Money - NNP Bank</title>
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

        /* Toast Notification Styles */
        .error_noti {
            position: fixed !important;
            top: 24px !important;
            right: 24px !important;
            min-width: 320px !important;
            max-width: 450px !important;
            padding: 14px 20px !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            color: #ffffff !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
            z-index: 999999 !important;
            animation: toastSlideIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }
        .error_noti i.bx-error,
        .error_noti i.bx-check-circle {
            font-size: 22px !important;
            flex-shrink: 0 !important;
        }
        .error_noti span {
            flex-grow: 1 !important;
            line-height: 1.4 !important;
        }
        .error_noti .icon2 {
            font-size: 20px !important;
            cursor: pointer !important;
            opacity: 0.85 !important;
            transition: opacity 0.2s, transform 0.2s !important;
            flex-shrink: 0 !important;
            margin-left: 8px !important;
        }
        .error_noti .icon2:hover {
            opacity: 1 !important;
            transform: scale(1.15) !important;
        }
        @keyframes toastSlideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<?php include "templets/side_nav.php"; ?>

<?php

    if(isset($_POST['amount']) || isset($_POST['Pin'])){

        #error function
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

        #cut money form account
        class withd{

            #user account
            function user_account($money){
                global $conn, $id;
                $check_amount = "SELECT * FROM `money_bank` WHERE `account_id` = '$id';";
                $amount_result = mysqli_query($conn, $check_amount);
                $amount_row = mysqli_num_rows($amount_result);

                if($amount_row == 1){
                    while($row = mysqli_fetch_assoc($amount_result)){
                        $ori_money = $row['amount'];
                    }
                    $add_amount = $ori_money - $money;
                    $add_money_sql = "UPDATE `money_bank` SET `amount`='$add_amount' WHERE `account_id` = '$id';";
                    $result_add_money = mysqli_query($conn, $add_money_sql);
                }
            }

        }

        #check pin
        function check($pin, $money){
            global $conn, $id;

            if($money > 0 ){
                #to store money
                $store_money = "SELECT * FROM `money_bank` WHERE `account_id` = '$id';";
                $result_money = mysqli_query($conn, $store_money);
                $money_row = mysqli_num_rows($result_money);
                $ori_money = 0;

                if($money_row == 1){
                    while($row = mysqli_fetch_assoc($result_money)){
                        $ori_money = (float)$row['amount'];
                    }
                }

                $chech_pin = "SELECT * FROM `account_details` WHERE `account_id` = '$id';";
                $result_pin = mysqli_query($conn, $chech_pin);
                $pin_row = mysqli_num_rows($result_pin);

                if($pin_row == 1){
                    while($row = mysqli_fetch_assoc($result_pin)){
                        if(trim($pin) == trim($row['pin'])){
                            if($money <= $ori_money){
                                $temp = new withd();

                                $temp->user_account($money);

                                #send email (commented out)
                                /*
                                $user_email = $_SESSION["email"] ?? '';
                                if(!empty($user_email)){
                                    $title = "Debited Money";
                                    $desc = "Your A/C can be Debited with Rs ".$money;
                                    mail_sender($user_email, $title, $desc);
                                }
                                */

                                #entey in history
                                $date = date('Y-m-d H:i:s');
                                $entery = "INSERT INTO `history`(`account_id`, `paymant_status`, `desc`, `time`, `amount`) VALUES ('$id','Debited','Withdrawal Money','$date','$money');";
                                $result_entery = mysqli_query($conn, $entery);
                                $error = "Withdraw successfully";
                                error_display($error, "green");
                            }
                            else{
                                $error = "not have that much amount in account";
                                error_display($error, "red");    
                            }
                        }
                        else{
                            $error =  "wrong pin";
                            error_display($error, "red");
                        }
                    }
                }
                else{
                    $error = "Account details not found";
                    error_display($error, "red");
                }
            }
            else{
                $error = "Invalid Amount";
                error_display($error, "red");
            }
        }

        #main
        $pin = $_POST['Pin'] ?? '';
        $money = floatval($_POST['amount'] ?? 0);

        check($pin, $money);

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

            <form action="with.php" method="post">
                <div>
                    <span>
                        <p class="text">Pin</p>
                        <input type="password" name="Pin" required>
                    </span>
                </div>

                <div id="amount_margin">
                    <span>
                        <p class="text">Amount</p>
                        <input type="number" name="amount" required>
                    </span>
                </div>
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