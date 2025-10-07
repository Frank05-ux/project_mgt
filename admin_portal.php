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
                header("Location: admin_dashboard.php");
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
<title>Admin Portal</title>
<style>
/* Background & Body */
body {
    margin:0;
    padding:0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                url('https://images.unsplash.com/photo-1583267747355-4e67cbeabf2c?auto=format&fit=crop&w=1950&q=80') 
                center/cover no-repeat fixed;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    color:#fff;
}

/* Login Box */
.login-box {
    background: rgba(46,52,64,0.9);
    padding:50px 40px;
    border-radius:15px;
    width:360px;
    text-align:center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
}

.login-box h2 {
    margin-bottom:30px;
    color:#88c0d0;
    text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
    font-size:28px;
}

.login-box input {
    width:100%;
    padding:12px;
    margin:12px 0;
    border-radius:8px;
    border:1px solid #ccc;
    box-sizing:border-box;
}

.login-box button {
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:#88c0d0;
    color:#2e3440;
    font-weight:bold;
    cursor:pointer;
    margin-top:20px;
    font-size:16px;
    transition:0.3s;
}

.login-box button:hover {
    background:#81a1c1;
}

.error-msg {
    color:#bf616a;
    margin-bottom:15px;
    font-weight:bold;
}
</style>
</head>
<body>

<div class="login-box">
    <h2>Admin Portal</h2>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
