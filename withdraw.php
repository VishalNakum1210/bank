<?php
    session_start();
    include "templets/_dbconnect.php";
    // include "email/test.php";
    if(!isset($_SESSION['login'])){
        header("location: login.php");
        exit();
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
    <link rel="icon" type="image/png" href="pic/logo.png">
    <link rel="shortcut icon" href="pic/logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style/withdraw.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style/side_nav.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Money - NNP Bank</title>
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

        #cut money from account
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
                                error_display($error);
                            }
                        }
                        else{
                            $error = "wrong pin";
                            error_display($error);
                        }
                    }
                }
                else {
                    $error = "Account details not found";
                    error_display($error, "red");
                }
            }
            else{
                $error = "Invalid Amount";
                error_display($error, "red");
            }
        }

        #main section
        $pin = $_POST['Pin'];
        $money = $_POST['amount'];

        check($pin, $money);

    }
?>

    <div class="card1">
        <div class="card-column">
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
                    <img src="pic/chip.png" class="chip-lg" alt="Chip">
                    <img src="pic/visa.png" class="visa-lg" alt="Visa">
                </div>
                <div class="card-number"><?php echo htmlspecialchars($account_number ?? ''); ?></div>
                <div class="card-name"><?php echo ucwords(htmlspecialchars($account_holder ?? '')); ?></div>
                <div class="card-footer">
                    <div class="amt-display">Balance<br>&#8377;<?php echo number_format((float)($money ?? 0), 2); ?></div>
                    <img src="pic/logo.png" class="bank-logo" alt="Bank Logo">
                </div>
            </div>
        </div>

        <div class="input_line">
            <div class="title_line">
                <p>Withdraw Money</p>
            </div>

            <form action="withdraw.php" method="post">
                <div class="line1">
                    <span>
                        <p class="text">Amount</p>
                        <input type="number" placeholder="Enter amount to withdraw" name="amount" required>
                    </span>
                </div>

                <div class="line1">
                    <span>
                        <p class="text">Pin</p>
                        <input type="password" placeholder="Enter 4-digit PIN" name="Pin" required>
                    </span>
                </div>

                <div class="btn-group">
                    <button type="submit" id="sub">Submit</button>
                    <a href="index.php" class="back_link">
                        <button type="button" id="back">Back</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>