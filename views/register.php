
<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<div class="main-content">
    <div class="auth-container">
        
        <form class="auth-form" action="/PHP_GolfHandicapCalculator/public/register" method="post">
            <h1>Register</h1>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required>

            <label for="age">Age:</label>
            <input type="number" name="age" id="age" required>

            <label for="gender">Gender:</label>
            <select name="gender" id="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Register</button>
            <p>Already have an account? <a href="/PHP_GolfHandicapCalculator/public/login">Login here</a>.</p>

        </form>
    </div>
</div>

