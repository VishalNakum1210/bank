<?php
    session_start();

    #include database connection
    include "templets/_dbconnect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="pic/logo.png">
    <link rel="shortcut icon" href="pic/logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style/register.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - NNP Bank</title>
</head>
<body>

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

        if(isset($_POST['username'])){

            #class section

            class insertData{

                function check($username, $age, $mail){
                    global $conn;
                    $check_username = "SELECT * FROM `money_bank` WHERE `username` = '$username';";
                    $result_username = mysqli_query($conn, $check_username);
                    $count_row = mysqli_num_rows($result_username);

                    if($count_row == 0){

                        #check E-mail
                        $chech_mail = "SELECT * FROM `persnol` WHERE `email` = '$mail';";
                        $result_mail = mysqli_query($conn, $chech_mail);
                        $mail_row = mysqli_num_rows($result_mail);

                        if($mail_row == 0){

                            if($age < 18){
                                return 3;
                            }
                            return 1;

                        }
                        else{
                            return 4;
                        }

                    }
                    else{
                        return 2;
                    }
                }

                function insert_persnol($name, $mobile_number, $mail){
                    global $conn;
                    $persnol_insert_data = "INSERT INTO `persnol`(`name`, `mobile_number`, `email`) VALUES ('$name','$mobile_number','$mail');";
                    $result_persnol = mysqli_query($conn, $persnol_insert_data);
                }

                function insert_account_details($account_type, $age, $pin){
                    global $conn;
                    $rand1 = rand(8000,9999);
                    $rand2 = rand(0001,9999);
                    $rand3 = rand(0001,9999);
                    $rand4 = rand(0001,9999);

                    $account_number = "$rand1 $rand2 $rand3 $rand4";
                    $account_detail_insert = "INSERT INTO `account_details`(`bank_name`, `account_number`, `account_type`, `age`, `pin`) VALUES ('NNP Bank','$account_number','$account_type','$age','$pin');";
                    $result_account_detail = mysqli_query($conn ,$account_detail_insert);
                }

                function insert_monery_bank($username, $password){
                    global $conn;
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $money_bank_insert = "INSERT INTO `money_bank`(`username`, `user_password`, `amount`) VALUES ('$username','$password',0);";
                    $result_money_bank = mysqli_query($conn, $money_bank_insert);
                }
                
            }


            #main section

            $name = $_POST['name'];
            $mobile = $_POST['mobile_number'];
            $mail = $_POST['mail'];
            $age = $_POST['age'];
            $account_type = $_POST['account_type'];
            $pin = $_POST['pin'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            $temp = new insertData();
            $condition = $temp->check($username, $age, $mail);

            if($condition == 1){
                $temp->insert_persnol($name, $mobile, $mail);
                $temp->insert_account_details($account_type, $age, $pin);
                $temp->insert_monery_bank($username, $password);

                #sesstion
                $_SESSION['login'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $mail;

                #to store id in session
                $check_id = "SELECT * FROM `money_bank` WHERE `username` = '$username';";
                $result_store_id = mysqli_query($conn, $check_id);
                $row_id = mysqli_num_rows($result_store_id);

                if($row_id == 1){
                    while($row = mysqli_fetch_assoc($result_store_id)){
                        $_SESSION['id'] = $row['account_id'];
                        header("location: index.php");
                    }
                }
                else{
                    $error = "something has wrong";
                    error_display($error);
                }
            }

            elseif($condition == 3){
                $error = "You are not eligible for opening account (your agr is under 18)";
                error_display($error);
            }

            elseif($condition == 4){
                $error = "email already in use";
                error_display($error);
            }

            elseif($condition == 2){
                $error = "Username is alredy taken by other";
                error_display($error);
            }
            else{
                $error = "Someting went wrong. please try again.";
            }

        }
    ?>


    <div class="card">

        <div class="title_line">
            <p>Registration</p>
        </div>

        <form action="register.php" method="post">
            <div class="line">

                <span>
                    <p class="text">Name</p>
                    <input type="text" name="name" required>
                </span>
    
                <span>
                    <p class="text">Mobile Number</p>
                    <input type="number" name="mobile_number" required>
                </span>
    
                <span>
                    <p class="text">E-mail</p>
                    <input type="email" name="mail" required>
                </span>
    
            </div>
    
            <div class="line">
    
                <span>
                    <p class="text">Age</p>
                    <input type="number" name="age" required>
                </span>
    
                <span>
                    <p class="text">Account type</p>
                    <input type="text" name="account_type" required>
                </span>
    
                <span>
                    <p class="text">Pin</p>
                    <input type="number" name="pin" required>
                </span>
    
            </div>
    
            <div class="line">
    
                <span>
                    <p class="text">Username</p>
                    <input type="text" name="username" required>
                </span>
    
                <span>
                    <p class="text">Password</p>
                    <input type="text" name="password" required>
                </span>
    
            </div>
    
            <button id="sub">Submit</button>
        </form>


        <a href="index.php">
            <button id="back">Back</button>
        </a>

    </div>
</body>
</html>