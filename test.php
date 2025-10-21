<?php
$conn = new mysqli("localhost", "root", "", "hospital_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result && $result->num_rows > 0) {
    echo "✅ 'users' table exists.";
} else {
    echo "❌ 'users' table NOT found in 'queue_db'.";
}

$conn->close();
?>
