<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: admin_login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin_Dashboard</title>
<style>
body{ font-family:sans-serif; background:#1e1e2f; color:#fff; }
.container{ max-width:800px; margin:50px auto; text-align:center; }
a{ display:inline-block; padding:10px 20px; margin:10px; background:#00bfff; color:#fff; text-decoration:none; border-radius:8px; }
</style>
</head>
<body>
<div class="container">
<h1>Welcome, <?= htmlspecialchars($_SESSION['admin']) ?>!</h1>
<a href="inventory.php">Inventory</a>
<a href="sales.php">Sales</a>
<a href="customers.php">Customers</a>
<a href="reports.php">Reports</a>
<a href="logout.php">Logout</a>
</div>
</body>
</html>
