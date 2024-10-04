<?php
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bookstore";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

// Use the new database
$conn->select_db($dbname);

// Create table
$sql = "CREATE TABLE IF NOT EXISTS books (
    BookID INT AUTO_INCREMENT PRIMARY KEY,
    BookName VARCHAR(255) NOT NULL,
    BookDescription TEXT NOT NULL,
    QuantityAvailable INT NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    ProductAddedBy VARCHAR(100) NOT NULL DEFAULT 'YourName',
    Author VARCHAR(255) NOT NULL,
    PublishedYear YEAR NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table books created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
