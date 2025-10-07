<?php
session_start();
include 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (hash('sha256', $password) === $admin['password']) {
                $_SESSION['admin'] = $admin['username'];
                header("Location: _dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }
        $stmt->close();
    } else {
        $error = "Please enter username and password.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<style>
/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Full-page high-quality car background with overlay */
body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: url('https://images.unsplash.com/photo-1605902711622-cfb43c443bf0?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
    position: relative;
}

body::before {
    content: '';
    position: absolute;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.5);
}

/* Bigger login box with glass effect */
.login-box {
    position: relative;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 50px 40px;
    border-radius: 20px;
    width: 400px; /* big size */
    text-align: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    z-index: 1;
}

.login-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.6);
}

/* Header */
.login-box h2 {
    font-size: 32px;
    color: #ffffff;
    margin-bottom: 30px;
    text-shadow: 1px 1px 8px rgba(0,0,0,0.7);
}

/* Inputs */
.login-box input {
    width: 100%;
    padding: 16px 15px;
    margin: 14px 0;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.4);
    background: rgba(255,255,255,0.1);
    color: #fff;
    font-size: 18px;
    outline: none;
    transition: border 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
}

.login-box input::placeholder {
    color: rgba(255,255,255,0.7);
}

.login-box input:focus {
    border: 1px solid #88c0d0;
    background: rgba(255,255,255,0.15);
    box-shadow: 0 0 10px #88c0d0aa;
}

/* Button */
.login-box button {
    width: 100%;
    padding: 16px 0;
    margin-top: 25px;
    border: none;
    border-radius: 25px;
    background: #88c0d0;
    color: #2e3440;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.login-box button:hover {
    background: #81a1c1;
    transform: translateY(-2px);
}

/* Error message */
.error-msg {
    background: #bf616a;
    padding: 14px 15px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    text-align: center;
}

/* Responsive */
@media (max-width: 480px) {
    .login-box {
        width: 90%;
        padding: 35px 25px;
    }

    .login-box h2 {
        font-size: 28px;
    }

    .login-box input {
        font-size: 16px;
        padding: 14px 12px;
    }

    .login-box button {
        font-size: 18px;
        padding: 14px 0;
    }
}
</style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
