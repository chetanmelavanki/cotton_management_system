<?php
$servername = "localhost";  // Your database host, typically 'localhost'
$username = "root";         // Your database username, typically 'root' for local
$password = "ROOT";             // Your database password, typically empty for local
$dbname = "cotton_management_system";  // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
