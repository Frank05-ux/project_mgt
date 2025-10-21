<?php
session_start();

// ✅ Database Connection
$conn = new mysqli("localhost", "root", "", "hospital_db");
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "⚠️ Please enter your email and password.";
    } else {
        // ✅ Use prepared statement for security
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // ✅ Verify password securely
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // ✅ Always redirect to admin dashboard
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "🚫 User not found.";
        }

        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hospital Login</title>
<style>
* {
  margin: 0; padding: 0; box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s ease;
}
body {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(135deg, #0546a0, #70b7ff, #ffd0d0);
  background-image: url('hospital image/hospital.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

/* Floating fade animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.login-box {
  width: 370px;
  background: rgba(255,255,255,0.9);
  padding: 35px 30px;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
  animation: fadeIn 1s ease forwards;
}

h2 {
  text-align: center;
  margin-bottom: 25px;
  color: #0546a0;
  font-size: 28px;
  text-shadow: 0 0 10px #70b7ff;
}

.input-group {
  margin-bottom: 18px;
  position: relative;
}
label {
  font-size: 1rem;
  color: #0546a0;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
}
input {
  width: 100%;
  padding: 12px 38px 12px 38px;
  margin-top: 7px;
  border: 1px solid #b3d1ff;
  border-radius: 10px;
  outline: none;
  background: #f7fbff;
  font-size: 15px;
  color: #222;
}
input:focus {
  box-shadow: 0 0 8px #70b7ff;
}
.toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
  color: #70b7ff;
}

button {
  width: 100%;
  background: linear-gradient(90deg, #0546a0 0%, #70b7ff 100%);
  color: #fff;
  padding: 13px;
  border: none;
  border-radius: 10px;
  font-weight: bold;
  cursor: pointer;
  font-size: 17px;
  letter-spacing: 1px;
  box-shadow: 0 2px 8px rgba(112,183,255,0.15);
}
button:hover {
  background: linear-gradient(90deg, #70b7ff 0%, #0546a0 100%);
  transform: scale(1.04);
  box-shadow: 0 0 15px #70b7ff;
}

.error {
  color: #d8000c;
  background: #ffd2d2;
  border-left: 4px solid #ff4d4d;
  padding: 10px 12px;
  margin-bottom: 18px;
  border-radius: 8px;
  font-size: 1rem;
}

footer {
  text-align: center;
  margin-top: 20px;
  font-size: 0.8rem;
  color: #0546a0;
}
</style>
</head>
<body>
<div class="login-box">
  <h2>🏥 Hospital Login</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="input-group">
      <label for="email">📧 Email</label>
      <input type="email" id="email" name="email" placeholder="e.g. admin@hospital.com" required autocomplete="username">
    </div>

    <div class="input-group">
      <label for="password">🔒 Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
      <button type="button" class="toggle-password" onclick="togglePassword()" tabindex="-1">👁️</button>
    </div>

    <div style="text-align:right; margin-bottom:12px;">
      <a href="#" style="color:#0546a0; text-decoration:underline; font-size:0.95rem;">Forgot Password?</a>
    </div>

    <button type="submit" name="login">Login</button>
  </form>

  <footer>© <?= date('Y') ?> Hospital Management System</footer>
</div>

<script>
function togglePassword() {
  const pwd = document.getElementById('password');
  const btn = document.querySelector('.toggle-password');
  if (pwd.type === 'password') {
    pwd.type = 'text';
    btn.textContent = '🙈';
  } else {
    pwd.type = 'password';
    btn.textContent = '👁️';
  }
}
</script>
</body>
</html>
