<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /PHP_GolfHandicapCalculator/public/login');
    exit();
}

// Include necessary layout files
include 'layouts/sidebar.php';

// Fetch user data from the database
require_once '../models/User.php';


if (!$user) {
    echo "No user found!";
    exit();
}
?>

<div class="main-content">
    <div class="profile-container">
        <h1>Profile</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>
        
        <div class="profile-section">
            <h2>Update Profile</h2>
            <form method="post" action="/PHP_GolfHandicapCalculator/public/update_profile">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="Male" <?php if ($user['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($user['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if ($user['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <button type="submit">Update Profile</button>
            </form>
        </div>

        <div class="password-section">
            <h2>Change Password</h2>
            <form method="post" action="/PHP_GolfHandicapCalculator/public/change_password">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>
</div>
