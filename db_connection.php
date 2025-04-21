<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Your database username
$password = "";     // Your database password (empty by default in XAMPP)
$dbname = "student_card_system"; // Your database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
