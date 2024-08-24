<?php

class Handicap {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addHandicap($user_id, $course_id, $handicap_index, $handicap, $date) {
        $query = "INSERT INTO handicap (user_id, course_id, handicap_index, handicap, date_calculated) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iidds', $user_id, $course_id, $handicap_index, $handicap, $date);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new mysqli_sql_exception("Error inserting handicap: " . $stmt->error);
        }
    }
}
