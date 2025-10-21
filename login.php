<?php
session_start();
$error = "";
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $conn = new mysqli("localhost", "root", "", "hospital_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if ($email === '' || $password === '') {
        $error = "⚠️ Please enter your email and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'patient'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'] ?? $user['email'];
        $_SESSION['role'] = $user['role'];
        header("Location: patient_dashboard.php");
        exit();
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "🚫 User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Login | Hospital Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  padding: 0;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.login-container {
  background: rgba(255,255,255,0.95);
  border-radius: 22px;
  box-shadow: 0 8px 32px rgba(112,183,255,0.13);
  padding: 40px 32px 32px 32px;
  text-align: center;
  max-width: 370px;
  width: 100%;
}
h2 {
  font-size: 2rem;
  color: #0546a0;
  margin-bottom: 18px;
  font-weight: 700;
  letter-spacing: 1px;
}
form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}
label {
  font-size: 1rem;
  color: #0546a0;
  font-weight: 500;
  text-align: left;
  margin-bottom: 4px;
}
input {
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #b3d1ff;
  background: #f7fbff;
  font-size: 1rem;
  color: #222;
  outline: none;
  transition: box-shadow 0.3s;
}
input:focus {
  box-shadow: 0 0 8px #70b7ff;
}
button {
  padding: 13px;
  background: linear-gradient(90deg, #0546a0 0%, #70b7ff 100%);
  color: #fff;
  font-weight: 600;
  border-radius: 10px;
  border: none;
  font-size: 1.08rem;
  box-shadow: 0 2px 8px rgba(112,183,255,0.10);
  transition: background 0.3s, transform 0.3s;
  cursor: pointer;
}
button:hover {
  background: linear-gradient(90deg, #70b7ff 0%, #0546a0 100%);
  transform: scale(1.04);
}
.error {
  margin-bottom: 18px;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 1rem;
  background: #ffd2d2;
  color: #d8000c;
  border-left: 4px solid #d8000c;
}
</style>
</head>
<body>
  <div class="login-container">
    <h2>Patient Login</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="" autocomplete="on">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="e.g. patient@email.com" required autocomplete="username">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
      <button type="submit" name="login">Login</button>
    </form>
    <div style="margin-top:18px;">
      <a href="register.php" style="color:#0546a0; text-decoration:underline; font-size:0.95rem;">Don't have an account? Register</a>
    </div>
  </div>
</body>
</html>
