<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Assuming you're using the default XAMPP password, which is an empty string
$dbname = "golf_handicap_calculator";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
