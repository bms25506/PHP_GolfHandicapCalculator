<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Score.php';
require_once '../models/Handicap.php';

class ScoreController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to render the score entry form and handle form submission
    public function enterScore() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        // Fetch courses for the dropdown
        $query = "SELECT id, tee_name, course_name, gender, slope_rating, course_rating, par, back_nine_rating FROM courses ORDER BY course_name, tee_name, gender";
        $result = $this->conn->query($query);
        $courses = $result->fetch_all(MYSQLI_ASSOC);
    
        // Initialize variables
        $course_id = '';
        $scores = array_fill(0, 18, ''); // Initialize an array for 18 scores
        $handicapIndex = null;
        $handicap = null;
        $holes = 18; // Default to 18 holes
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_id = $_POST['course_id'];
    
            // Fetch the selected course data
            $query = "SELECT * FROM courses WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $course_id);
            $stmt->execute();
            $course = $stmt->get_result()->fetch_assoc();
    
            // Determine the number of holes
            $holes = (!empty($course['back_nine_rating']) && $course['back_nine_rating'] !== '') ? 18 : 9;
    
            // Get scores from the form
            $scores = [];
            for ($i = 0; $i < $holes; $i++) {
                $scores[$i] = $_POST['score_' . $i];
            }
    
            // Calculate the total score and Handicap Index
            $total_score = array_sum($scores);  // Sum the individual hole scores to get the total score
            if ($total_score == 0) {
                throw new Exception("Total score cannot be zero.");
            }
    
            // Calculate the Handicap Index and Handicap
            $handicapIndex = $total_score / $holes;
            $handicap = ($handicapIndex * ($course['slope_rating'] / 113)) + ($course['course_rating'] - $course['par']);
    
            // Save the total score and other data to the database
            $user_id = $_SESSION['user_id'];
            $date_played = date('Y-m-d H:i:s');  // Ensure correct format for DATETIME field
    
            // Save the total score and other data to the database
            $query = "INSERT INTO scores (user_id, course_id, score, holes, date_played, created_at, handicap_index, handicap) 
                      VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('iiissdd', $user_id, $course_id, $total_score, $holes, $date_played, $handicapIndex, $handicap);
            $stmt->execute();
    
            // Save the handicap data in the handicap table
            $query = "INSERT INTO handicap (user_id, course_id, date_calculated, handicap_index, handicap) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('iisdd', $user_id, $course_id, $date_played, $handicapIndex, $handicap);
            $stmt->execute();
    
            // Store handicap in session for display after redirect
            $_SESSION['handicapIndex'] = $handicapIndex;
            $_SESSION['handicap'] = $handicap;
    
            // Redirect to avoid form resubmission
            header('Location: /PHP_GolfHandicapCalculator/public/enter_score?success=1');
            exit();
        }
    
        // Fetch the most recent handicap for the user to display after redirect or on page load
        $user_id = $_SESSION['user_id'];
        $query = "SELECT handicap_index, handicap, date_calculated FROM handicap WHERE user_id = ? ORDER BY date_calculated DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $recentHandicap = $stmt->get_result()->fetch_assoc();
    
        // Include the view
        include '../views/enter_score.php';
    }
                
        
    
    
    
    

    // Function to render the score history form and table
    public function viewScoreHistory() {
        $searchCourse = '';
        $searchDate = '';
        $orderBy = 'date_played'; // Default sorting by date
        $orderDirection = 'DESC'; // Default sorting order

        // Process search and sort inputs
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $searchCourse = $_POST['search_course'];
            $searchDate = $_POST['search_date'];
            $orderBy = $_POST['order_by'];
            $orderDirection = $_POST['order_direction'];
        }

        // Build the query
        $query = "SELECT scores.*, courses.course_name, courses.tee_name FROM scores 
                  JOIN courses ON scores.course_id = courses.id 
                  WHERE user_id = ?";

        // Add search filters
        if (!empty($searchCourse)) {
            $query .= " AND courses.course_name LIKE ?";
        }
        if (!empty($searchDate)) {
            $query .= " AND scores.date_played = ?";
        }

        // Add sorting
        $query .= " ORDER BY $orderBy $orderDirection";

        // Prepare and execute the query
        $stmt = $this->conn->prepare($query);
        $searchCourseWildcard = "%$searchCourse%";
        if (!empty($searchCourse) && !empty($searchDate)) {
            $stmt->bind_param('iss', $_SESSION['user_id'], $searchCourseWildcard, $searchDate);
        } elseif (!empty($searchCourse)) {
            $stmt->bind_param('is', $_SESSION['user_id'], $searchCourseWildcard);
        } elseif (!empty($searchDate)) {
            $stmt->bind_param('is', $_SESSION['user_id'], $searchDate);
        } else {
            $stmt->bind_param('i', $_SESSION['user_id']);
        }

        $stmt->execute();
        $scores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Load the view file to display the form and table
        include '../views/score_history.php';
    }

    public function viewHandicap() {
        $user_id = $_SESSION['user_id'];
    
        // Fetch handicap information from the database
        $query = "SELECT handicap.*, courses.course_name, courses.tee_name 
                  FROM handicap 
                  JOIN courses ON handicap.course_id = courses.id 
                  WHERE handicap.user_id = ?
                  ORDER BY handicap.date_calculated DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $handicaps = $result->fetch_all(MYSQLI_ASSOC);
    
        include '../views/view_handicap.php';
    }
}
