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
    <link rel="stylesheet" href="style/demo.css">
    <link rel="stylesheet" href="style/side_nav.css?v=2">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .card-wrapper {
            perspective: 1000px;
            margin-bottom: 40px;
        }
        .card {
            background: url('pic/bg6.jpg') center/cover !important;
            position: relative;
            width: 450px;
            height: 250px;
            border-radius: 20px;
            padding: 30px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 30px 60px rgba(0,0,0,0.8);
            overflow: hidden;
            transition: transform 0.1s ease-out;
            cursor: pointer;
            transform-style: preserve-3d;
        }
        .card::before {
            content: ''; position: absolute; top:0; left:0; right:0; bottom:0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            z-index: 1;
        }
        
        .card-chip-row { display: flex; justify-content: space-between; position: relative; z-index: 2; width: 100%; }
        .chip-lg { height: 45px; }
        .visa-lg { height: 28px; filter: brightness(0) invert(1); }
        .card-number { font-size: 26px; font-weight: 700; letter-spacing: 4px; position: relative; z-index: 2; text-align: center; text-shadow: 0 4px 8px rgba(0,0,0,0.6); color: white; margin: 0;}
        .card-name { font-size: 20px; font-weight: 600; text-transform: uppercase; position: relative; z-index: 2; letter-spacing: 1px; color: white; margin: 0;}
        .card-footer { display: flex; justify-content: space-between; align-items: flex-end; position: relative; z-index: 2; color: white; margin: 0; width: 100%;}
        .bank-logo { height: 50px; }
        .amt-display { font-size: 13px; font-weight: 600; text-transform: uppercase;}
    </style>
</head>
<body>
    <?php include "templets/side_nav.php"; ?>

    <?php
        if($_SESSION['login'] == true){
            
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
        }
    ?>

    <div class="card-wrapper">
        <div class="card">
            <div class="card-chip-row">
                <img src="pic/chip.png" class="chip-lg">
                <img src="pic/visa.png" class="visa-lg">
            </div>
            <div class="card-number"><?php echo htmlspecialchars($account_number ?? ''); ?></div>
            <div class="card-name"><?php echo ucwords(htmlspecialchars($account_holder ?? '')); ?></div>
            <div class="card-footer">
                <div class="amt-display">Valid<br>12/25</div>
                <img src="pic/logo.png" class="bank-logo">
            </div>
        </div>
    </div>

    <a href="index.php">
        <button id="back">Back</button>
    </a>

    <script>
        const card = document.querySelector('.card');
        
        card.addEventListener('mouseenter', () => {
            card.style.transition = 'transform 0.1s ease-out';
        });

        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const maxRotate = 15;
            
            const rotateY = -((x - centerX) / centerX) * maxRotate;
            const rotateX = ((y - centerY) / centerY) * maxRotate;
            
            card.style.transform = `scale(1.05) translateY(-10px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transition = 'transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            card.style.transform = `scale(1) translateY(0px) rotateX(0deg) rotateY(0deg)`;
        });
    </script>
</body>
</html>