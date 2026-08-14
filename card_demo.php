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

// Fetch holder name
$account_holder = $username;
$stmt_p = mysqli_prepare($conn, "SELECT name FROM `persnol` WHERE `account_id` = ?");
mysqli_stmt_bind_param($stmt_p, "i", $id);
mysqli_stmt_execute($stmt_p);
$res_p = mysqli_stmt_get_result($stmt_p);
if ($row_p = mysqli_fetch_assoc($res_p)) {
    $account_holder = $row_p['name'];
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
    <link rel="stylesheet" href="assets/css/card_demo.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/side_nav.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Card Demo - NNP Bank</title>
    <style>
        .card-wrapper {
            perspective: 1000px;
            margin-bottom: 40px;
        }
        .card {
            background: url('assets/images/bg6.jpg') center/cover !important;
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
    <?php include "includes/sidebar.php"; ?>

    <div class="card-wrapper">
        <div class="card">
            <div class="card-chip-row">
                <img src="assets/images/chip.png" class="chip-lg" alt="Chip">
                <img src="assets/images/visa.png" class="visa-lg" alt="Visa">
            </div>
            <div class="card-number"><?php echo htmlspecialchars($account_number); ?></div>
            <div class="card-name"><?php echo ucwords(htmlspecialchars($account_holder)); ?></div>
            <div class="card-footer">
                <div class="amt-display">Valid<br>12/28</div>
                <img src="assets/images/logo.png" class="bank-logo" alt="Bank Logo">
            </div>
        </div>
    </div>

    <a href="index.php">
        <button id="back">Back to Dashboard</button>
    </a>

    <script>
        const card = document.querySelector('.card');
        if (card) {
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
        }
    </script>
</body>
</html>