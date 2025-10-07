<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $full_name = $_POST['full_name'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];

  // Check if email already exists
  $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $msg = "⚠️ Email already registered!";
  } else {
    $stmt = $conn->prepare("INSERT INTO customers (full_name, phone, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $full_name, $phone, $email);
    if ($stmt->execute()) {
      $msg = "✅ Registration successful! You can now log in.";
    } else {
      $msg = "❌ Registration failed.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Registration</title>
<style>
body {
  background: url('https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
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
  background: #ff9800;
  color: #fff;
  font-size: 17px;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
  transition: background 0.3s;
}
button:hover { background: #e68900; }
.msg { text-align:center; margin-bottom:10px; }
a { color:#ffb84d; text-decoration:none; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<form method="POST">
  <h2>Register Account</h2>
  <?php if(!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>
  <input type="text" name="full_name" placeholder="Full Name" required>
  <input type="text" name="phone" placeholder="Phone Number" required>
  <input type="email" name="email" placeholder="Email Address" required>
  <button type="submit">Register</button>
  <p style="text-align:center;margin-top:15px;">Already registered? <a href="user_login.php">Login</a></p>
</form>
</body>
</html>
