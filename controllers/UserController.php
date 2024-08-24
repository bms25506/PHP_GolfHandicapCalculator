<?php
require '../models/User.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        // Pass the database connection to the User model
        $this->userModel = new User($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user_id = $this->userModel->login($username, $password);

            if ($user_id) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                header('Location: /PHP_GolfHandicapCalculator/public/dashboard');
            } else {
                echo "Incorrect username or password.";
            }
        }

        include '../views/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $age = $_POST['age'];
            $gender = $_POST['gender'];
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $user_created = $this->userModel->register($first_name, $last_name, $age, $gender, $email, $username, $hashed_password);

                if ($user_created) {
                    header('Location: /PHP_GolfHandicapCalculator/public/login');
                    exit();
                } else {
                    echo "Registration failed. Please try again.";
                }
            } else {
                echo "Passwords do not match.";
            }
        }

        include '../views/register.php';
    }

    public function viewProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit();
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        include '../views/profile.php';
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $age = $_POST['age'];
            $gender = $_POST['gender'];
            $email = $_POST['email'];
            $username = $_POST['username'];

            $isUpdated = $this->userModel->updateUser($user_id, $first_name, $last_name, $age, $gender, $email, $username);

            if ($isUpdated) {
                $_SESSION['success_message'] = "Profile updated successfully.";
            } else {
                $_SESSION['error_message'] = "An error occurred. Please try again.";
            }

            header('Location: /PHP_GolfHandicapCalculator/public/profile');
            exit();
        }
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            $user = $this->userModel->getUserById($user_id);

            if (password_verify($current_password, $user['password'])) {
                if ($new_password === $confirm_password) {
                    $this->userModel->updatePassword($user_id, $new_password);
                    $_SESSION['success_message'] = "Password updated successfully.";
                } else {
                    $_SESSION['error_message'] = "New passwords do not match.";
                }
            } else {
                $_SESSION['error_message'] = "Current password is incorrect.";
            }

            header('Location: /PHP_GolfHandicapCalculator/public/profile');
            exit();
        }
    }

    public function logout() {
        // Unset all session variables
        session_unset();

        // Destroy the session
        session_destroy();

        // Redirect to the login page
        header('Location: /PHP_GolfHandicapCalculator/public/login');
        exit();
    }
}
