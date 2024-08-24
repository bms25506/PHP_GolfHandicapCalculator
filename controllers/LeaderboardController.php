<?php
require_once '../config/db.php';

class LeaderboardController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function viewLeaderboard() {
        // Query to get leaderboard data
        $query = "SELECT u.username, u.first_name, u.last_name, 
                         MIN(h.handicap) AS best_handicap, 
                         COUNT(s.id) AS rounds_played,
                         AVG(s.score) AS average_score
                  FROM users u
                  JOIN handicap h ON u.id = h.user_id
                  JOIN scores s ON u.id = s.user_id
                  GROUP BY u.id
                  ORDER BY best_handicap ASC, average_score ASC";

        $result = $this->conn->query($query);
        $leaderboard = $result->fetch_all(MYSQLI_ASSOC);

        // Include the view
        include '../views/leaderboard.php';
    }
}
