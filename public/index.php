<?php
// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load the necessary files
require '../config/db.php';
require '../controllers/HomeController.php';
require '../controllers/UserController.php';
require '../controllers/DashboardController.php';
require '../controllers/ScoreController.php';
require '../controllers/LeaderboardController.php';

// Create a database connection
$db = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the requested URL path
$request = strtok(str_replace('/PHP_GolfHandicapCalculator/public', '', $_SERVER['REQUEST_URI']), '?');
$request = trim($request, '/');

// Determine if the sidebar should be visible
$sidebar_visible = false; // Default to no sidebar
global $sidebar_visible;

// Define routes
switch ($request) {
    case '':
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;
    case 'login':
        $controller = new UserController($db);
        $controller->login();
        break;
    case 'register':
        $controller = new UserController($db);
        $controller->register();
        break;
    case 'logout':
        $controller = new UserController($db);
        $controller->logout();
        break;
    case 'profile':
    case 'update_profile':
    case 'change_password':
    case 'dashboard':
    case 'enter_score':
    case 'calculate_handicap':
    case 'score_history':
    case 'view_handicap':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /PHP_GolfHandicapCalculator/public/login');
            exit();
        }
        $sidebar_visible = true;
        
        // Handle controller logic
        switch ($request) {
            case 'profile':
                $controller = new UserController($db);
                $controller->viewProfile();
                break;
            case 'update_profile':
                $controller = new UserController($db);
                $controller->updateProfile();
                break;
            case 'change_password':
                $controller = new UserController($db);
                $controller->changePassword();
                break;
            case 'dashboard':
                $controller = new DashboardController();
                $controller->viewDashboard();
                break;
            case 'enter_score':
                $controller = new ScoreController($db);
                $controller->enterScore();
                break;
            case 'calculate_handicap':
                $controller = new ScoreController($db);
                $controller->enterScore();
                break;
            case 'score_history':
                $controller = new ScoreController($db);
                $controller->viewScoreHistory();
                break;
            case 'view_handicap':
                $controller = new ScoreController($db);
                $controller->viewHandicap();
                break;
        }
        break;
    case 'about':
    case 'contact':
    case 'leaderboard':
        if (isset($_SESSION['user_id'])) {
            $sidebar_visible = true;
        }
        
        // Handle static pages
        include "../views/$request.php"; 
        break;
    default:
        include '../views/404.php';
        break;
}

// Include header.php before the sidebar logic
include '../views/layouts/header.php';

// Include sidebar if needed
if ($sidebar_visible) {
    include '../views/layouts/sidebar.php';
}

// Include footer.php
include '../views/layouts/footer.php';

// Close the database connection
$db->close();
?>
