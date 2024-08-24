<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require '../config/db.php';

// Handle the form submission for password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the new password and confirm password match
    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "New passwords do not match.";
        header('Location: profile.php');
        exit();
    }

    // Fetch the current password hash from the database
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify the current password
    if (!password_verify($current_password, $hashed_password)) {
        $_SESSION['error_message'] = "Current password is incorrect.";
        header('Location: profile.php');
        exit();
    }

    // Hash the new password
    $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $update_query = "UPDATE users SET password = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('si', $new_hashed_password, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Password changed successfully.";
    } else {
        $_SESSION['error_message'] = "An error occurred. Please try again.";
    }

    $update_stmt->close();
    header('Location: profile.php');
    exit();
}
?>
