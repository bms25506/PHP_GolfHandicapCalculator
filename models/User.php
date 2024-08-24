<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;  // Ensure the correct variable is used here
    }

    public function register($first_name, $last_name, $age, $gender, $email, $username, $hashed_password) {
        $query = "INSERT INTO users (first_name, last_name, age, gender, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);  // $conn is used correctly here
        $stmt->bind_param("ssissss", $first_name, $last_name, $age, $gender, $email, $username, $hashed_password);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new mysqli_sql_exception("Error: " . $stmt->error);
        }
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $id = null;
            $hashed_password = "";
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                return $id;
            }
        }
        return false;
    }

    public function getUserById($user_id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUser($user_id, $first_name, $last_name, $age, $gender, $email, $username) {
        $query = "UPDATE users SET first_name = ?, last_name = ?, age = ?, gender = ?, email = ?, username = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssisssi', $first_name, $last_name, $age, $gender, $email, $username, $user_id);
        return $stmt->execute();
    }

    public function updatePassword($user_id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $hashed_password, $user_id);
        return $stmt->execute();
    }
}
