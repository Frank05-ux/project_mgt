<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Logged Out</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: url('https://images.unsplash.com/photo-1618005198919-2e5862a36be1?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
    color: #fff;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
}
.container {
    background: rgba(0,0,0,0.7);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.8);
}
h1 {
    font-size: 36px;
    margin-bottom: 20px;
}
p {
    font-size: 20px;
    margin-bottom: 30px;
}
a {
    display: inline-block;
    padding: 12px 25px;
    background: #4CAF50;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-size: 18px;
}
a:hover { background: #45a049; }
</style>
</head>
<body>
<div class="container">
    <h1>👋 You have successfully logged out!</h1>
    <p>Thank you for visiting our car sales system.</p>
    <a href="user_login.php">🔑 Log in again</a>
</div>
</body>
</html>
