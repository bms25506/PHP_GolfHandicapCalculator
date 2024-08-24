<?php
class Profile {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProfile($userId) {
        $stmt = $this->conn->prepare("SELECT first_name, last_name, email, age, gender FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
