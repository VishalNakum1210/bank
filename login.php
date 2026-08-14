<?php
    session_start();
    include "templets/_dbconnect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style/login.css?v=2">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NNP Bank</title>
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

            #user exsist check function
            function check($username, $password){
                global $conn;
                $exsist_user = "SELECT * FROM `money_bank` WHERE `username` = '$username';";
                $result_exsist_user = mysqli_query($conn, $exsist_user);
                $user_row = mysqli_num_rows($result_exsist_user);

                if($user_row == 1){
                    while($row = mysqli_fetch_assoc($result_exsist_user)){
                        $pass = $row['user_password'];
                        if(password_verify($password, $pass)){
                            
                            #sesstion
                            $_SESSION['login'] = true;
                            $_SESSION['username'] = $username;

                            #to store id in session
                            $check_id = "SELECT * FROM `money_bank` WHERE `username` = '$username';";
                            $result_store_id = mysqli_query($conn, $check_id);
                            $row_id = mysqli_num_rows($result_store_id);

                            if($row_id == 1){
                                while($row = mysqli_fetch_assoc($result_store_id)){
                                    $_SESSION['id'] = $id = $row['account_id'];

                                    #email
                                    $check_email = "SELECT * FROM `persnol` WHERE `account_id` = '$id';";
                                    $result_store_email = mysqli_query($conn, $check_email);
                                    $row_id = mysqli_num_rows($result_store_email);

                                    if($row_id > 0){
                                        while($row = mysqli_fetch_assoc($result_store_email))
                                        $_SESSION['email'] = $row['email'];
                                    }
                                    header("location: index.php");
                                }
                            }
                            else{
                                $error = "something has wrong";
                                error_display($error);
                            }
                        }
                        else{
                            $error = "wrong password";
                            error_display($error);
                        }
                    }
                }
                else{
                    $error = "this username is not exsist";
                    error_display($error);
                }
            }

            #main section

            $username = $_POST['username'];
            $password = $_POST['password'];

            check($username, $password);
        }
    ?>
    <div class="card">

        <form action="login.php" method="post">
            <div class="line_title">
                <p>Login</p>
            </div>

            <div class="line">
                <p class="text">Username</p>
                <input type="text" name="username" required>
            </div>

            <div class="line">
                <p class="text">Password</p>
                <input type="password" name="password" required>
                <a href="forgot.php"><p class="forget">Forget password</p></a>
            </div>

            <button id="sub">Submit</button>
        </form>
        
        <button id="back">Back</button>

        <div class="registra">
            <p>Not registration? <a href="registration.php">click here</a></p>
        </div>
    </div>
</body>
</html>