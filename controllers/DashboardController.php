<?php

require_once '../models/Score.php';
require_once '../models/Handicap.php';


class DashboardController {
    private $db;

    public function __construct() {
        // Include the database connection file
        require_once '../config/db.php';  // Adjust the path as necessary

        // Make the $conn variable from db.php accessible here
        global $conn;

        // Assign the global $conn to $this->db
        $this->db = $conn;
    }

    public function viewDashboard() {
        // Ensure that $this->db is not null
        if ($this->db) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        $user_id = $_SESSION['user_id'];

        // Fetch the most recent handicap
        $query = "SELECT * FROM handicap WHERE user_id = ? ORDER BY date_calculated DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $recentHandicap = $stmt->get_result()->fetch_assoc();

        // Fetch the most recent score
        $query = "SELECT scores.*, courses.course_name, courses.tee_name 
                  FROM scores 
                  JOIN courses ON scores.course_id = courses.id 
                  WHERE user_id = ? 
                  ORDER BY date_played DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $recentScore = $stmt->get_result()->fetch_assoc();

        $summary = $this->getPerformanceSummary($_SESSION['user_id']);

        include '../views/dashboard.php';
        } else {
            die("Database connection failed.");
        }
    }

    public function getPerformanceSummary($user_id) {
        // Calculate the total score and total holes played
        $query = "SELECT SUM(score) as total_score, SUM(holes) as total_holes FROM scores WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
    
        $total_score = $result['total_score'];
        $total_holes = $result['total_holes'];
    
        // Calculate the average score per hole
        $average_score_per_hole = $total_holes ? ($total_score / $total_holes) : 0;
    
        return [
            'average_score_per_hole' => number_format($average_score_per_hole, 2),
            'total_holes' => $total_holes
        ];
    }
    
}
