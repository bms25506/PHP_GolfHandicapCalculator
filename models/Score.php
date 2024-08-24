<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Score {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addScore($user_id, $course_id, $total_score, $date_played, $holes, $handicapIndex, $handicap) {
        // Check if the entry already exists
        $checkQuery = "SELECT COUNT(*) as count FROM scores WHERE user_id = ? AND course_id = ? AND date_played = ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param('iis', $user_id, $course_id, $date_played);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if ($row['count'] > 0) {
            // Instead of throwing an exception, return false
            return false;
        }
    
        // If no entry exists, proceed with the insertion
        $query = "INSERT INTO scores (user_id, course_id, score, date_played, holes, handicap_index, handicap) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iiisdds', $user_id, $course_id, $total_score, $date_played, $holes, $handicapIndex, $handicap);
    
        if ($stmt->execute()) {
            return true;
        } else {
            throw new mysqli_sql_exception("Error inserting score: " . $stmt->error);
        }
    }
    
    
    
}
