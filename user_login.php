<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $phone = $_POST['phone'];

  $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ? AND phone = ?");
  $stmt->bind_param("ss", $email, $phone);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $_SESSION['customer_id'] = $user['id'];
    $_SESSION['customer_name'] = $user['full_name'];
    header("Location: user_dashboard.php");
    exit();
  } else {
    $msg = "❌ Invalid login credentials!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Login</title>
<style>
body {
  background: url('https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
  font-family: 'Segoe UI', Arial, sans-serif;
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
form {
  background: rgba(0,0,0,0.75);
  padding: 40px 50px;
  width: 400px;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(255,255,255,0.2);
}
h2 {
  text-align: center;
  margin-bottom: 25px;
  font-size: 28px;
  letter-spacing: 1px;
}
input {
  width: 100%;
  padding: 14px;
  margin: 10px 0;
  border-radius: 6px;
  border: none;
  font-size: 15px;
}
button {
  width: 100%;
  padding: 14px;
  background: #4CAF50;
  color: #fff;
  font-size: 17px;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
  transition: background 0.3s;
}
button:hover { background: #45a049; }
.msg { text-align:center; margin-bottom:10px; }
a { color:#7cffc1; text-decoration:none; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<form method="POST">
  <h2>User Login</h2>
  <?php if(!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>
  <input type="email" name="email" placeholder="Email Address" required>
  <input type="text" name="phone" placeholder="Phone Number" required>
  <button type="submit">Login</button>
  <p style="text-align:center;margin-top:15px;">New user? <a href="user_register.php">Register here</a></p>
</form>
</body>
</html>
