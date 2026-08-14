<!DOCTYPE html>
  <!-- Coding by CodingLab | www.codinglabweb.com -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <!--<title>Dashboard Sidebar Menu</title>--> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    
                </span>

                <div class="text logo-text">
                    <span class="name">NNP</span>
                    <span class="profession">Online banking</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="index.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="card_demo.php">
                            <i class='bx bx-credit-card icon'></i>
                            <span class="text nav-text">Card Demo</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="withdraw.php">
                            <i class='bx bx-money-withdraw icon'></i>
                            <span class="text nav-text">Money Withdraw</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="add_money.php">
                        <i class='bx bx-money icon'></i>
                            <span class="text nav-text">Add Money</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="transactions.php">
                        <i class='bx bx-history icon'></i>
                            <span class="text nav-text">History</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="transfer.php">
                        <i class='bx bx-transfer-alt icon'></i>
                            <span class="text nav-text">Transfer Money</span>
                        </a>
                    </li>

                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>

    </nav>
    <script>
        const body = document.querySelector('body'),
              sidebar = body.querySelector('nav.sidebar'),
              toggle = body.querySelector(".toggle"),
              searchBtn = body.querySelector(".search-box");

        if (toggle && sidebar) {
            toggle.addEventListener("click" , () =>{
                sidebar.classList.toggle("close");
            });
        }

        if (searchBtn && sidebar) {
            searchBtn.addEventListener("click" , () =>{
                sidebar.classList.remove("close");
            });
        }
    </script>

</body>
</html>