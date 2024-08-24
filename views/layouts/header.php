<?php
// Ensure $sidebar_visible is recognized as a global variable
$sidebar_visible = true;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golf Handicap Calculator</title>
    <!-- Link to your CSS file -->
    <link rel="stylesheet" href="/PHP_GolfHandicapCalculator/assets/css/styles.css">
     <!-- Include Font Awesome for icons -->
     <link rel="stylesheet" href="/PHP_GolfHandicapCalculator/assets/fontawesome/css/all.min.css">
     <style>
        

    </style>

</head>

<body class="<?php echo $sidebar_visible ? 'sidebar-visible' : ''; ?>">

    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/PHP_GolfHandicapCalculator/public/dashboard">Golf Handicap Calculator</a>
            </div>
            <nav class="main-nav">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i> <!-- Mobile menu icon -->
                </button>
                <ul id="navMenu">
                    <li><a href="/PHP_GolfHandicapCalculator/public/home">Home</a></li>
                    <li><a href="/PHP_GolfHandicapCalculator/public/about">About</a></li>
                    <li><a href="/PHP_GolfHandicapCalculator/public/leaderboard">Leaderboard</a></li>
                    <li><a href="/PHP_GolfHandicapCalculator/public/contact">Contact</a></li>
                    <li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="/PHP_GolfHandicapCalculator/public/logout">Logout</a>
                        <?php else: ?>
                            <a href="/PHP_GolfHandicapCalculator/public/login">Login</a>
                        <?php endif; ?>  
                    </li>
            
                </ul>
            </nav>
        </div>
    </header>

