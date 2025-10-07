<?php
// Start session
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Database configuration
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "admin_db"; // make sure your DB name is correct

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if($conn->connect_error){
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8");
?>
