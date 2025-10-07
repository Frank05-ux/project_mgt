<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Car Sales System Links</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: url('images/cars/dashboard_bg.jpeg') center/cover no-repeat fixed;
    color: #fff;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 600px;
    margin: 100px auto;
    padding: 20px;
    background: rgba(0,0,0,0.8);
    border-radius: 10px;
    text-align: center;
}
h1 {
    margin-bottom: 30px;
    font-size: 28px;
    color: #ffeb3b;
}
a.link-btn {
    display: block;
    margin: 10px 0;
    padding: 12px 20px;
    background: #2196F3;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-size: 18px;
    transition: 0.3s;
}
a.link-btn:hover {
    background: #1976D2;
}
</style>
</head>
<body>
<div class="container">
    <h1>🚗 Car Sales System Links</h1>
    
    <a class="link-btn" href="user_login.php">🔑 User Login</a>
    <a class="link-btn" href="user_dashboard.php">🏠 User Dashboard</a>
    <a class="link-btn" href="logout.php">🚪 Logout</a>
    
    <hr style="margin:20px 0; border:1px solid #555;">
    
    <a class="link-btn" href="admin_login.php">🛠 Admin Login</a>
    <a class="link-btn" href="admin_dashboard.php">📊 Admin Dashboard</a>
    <a class="link-btn" href="customers.php">✉ Customer Messages</a>
    <a class="link-btn" href="sales.php">💰 Sales Report</a>
</div>
</body>
</html>
