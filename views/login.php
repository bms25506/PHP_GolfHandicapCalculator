<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<div class="main-content">
    <div class="auth-container">
        <form action="/PHP_GolfHandicapCalculator/public/login" method="post">
            <h1>Login</h1>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="/PHP_GolfHandicapCalculator/public/register">Register here</a>.</p>
 
        </form>
    </div>
</div>


