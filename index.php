<?php
session_start();
include 'db_connect.php'; // Your DB connection

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// If already logged in, redirect to dashboard
if(isset($_SESSION['username']) && !isset($_GET['login'])) {
    header("Location: index.php?page=dashboard");
    exit();
}

// Handle login form submission
$error = '';
if(isset($_POST['login'])){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 1){
        $user = $result->fetch_assoc();
        // Plain text for demo; use password_hash in production
        if($password === $user['password']){
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php?page=dashboard");
            exit();
        }else{
            $error = "Invalid password.";
        }
    }else{
        $error = "Username not found.";
    }
}

// Determine page
$page = $_GET['page'] ?? 'welcome';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Frank Sales Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* General Styles */
body { margin:0; font-family: Arial, sans-serif; }
a { text-decoration:none; }
h1,h2,p { margin:0; padding:0; }

/* Welcome Page */
#welcome { 
    display: <?php echo $page==='welcome'?'flex':'none'; ?>;
    background: url('images/welcome_bg.jpg') center/cover no-repeat;
    color:#fff; height:100vh; flex-direction:column;
    justify-content:center; align-items:center; text-align:center;
}
#welcome h1 { font-size: 48px; margin-bottom: 20px; text-shadow:2px 2px 5px #000; }
#welcome p { font-size: 24px; margin-bottom: 40px; text-shadow:1px 1px 3px #000; }
#welcome a { padding: 15px 30px; background:#3498db; color:#fff; border-radius:5px; font-size:20px; transition:0.3s; }
#welcome a:hover { background:#2980b9; }

/* Login Page */
#login { 
    display: <?php echo $page==='login'?'flex':'none'; ?>;
    background: url('images/login_bg.jpg') center/cover no-repeat; height:100vh;
    justify-content:center; align-items:center; 
}
.login-container {
    background: rgba(0,0,0,0.7); padding:40px; border-radius:10px; color:#fff;
    width: 350px; text-align:center;
}
.login-container h2 { margin-bottom: 30px; }
.login-container input {
    width: 100%; padding: 12px; margin:10px 0; border-radius:5px; border:none;
}
.login-container button {
    width:100%; padding:12px; background:#3498db; border:none; color:#fff; font-size:16px; border-radius:5px;
    cursor:pointer;
}
.login-container button:hover { background:#2980b9; }
.error { color:#e74c3c; margin-bottom:10px; }

/* Dashboard Page */
#dashboard { 
    display: <?php echo $page==='dashboard'?'flex':'none'; ?>;
    background: url('images/dashboard_bg.jpg') center/cover no-repeat; background-size:cover; color:#fff;
}
.sidebar {
    width:220px; background:#2c3e50; color:#fff; height:100vh; position:fixed; display:flex; flex-direction:column;
}
.sidebar h2 { text-align:center; padding:20px 0; border-bottom:1px solid #34495e; }
.sidebar a { display:flex; align-items:center; padding:15px 20px; color:#fff; border-bottom:1px solid #34495e; }
.sidebar a i { margin-right:10px; }
.sidebar a:hover { background:#34495e; }
.main { margin-left:220px; padding:20px; width:100%; }
.dashboard-cards { display:grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
.card { background: rgba(255,255,255,0.9); color:#333; padding:30px; border-radius:10px; text-align:center; box-shadow:0 0 10px rgba(0,0,0,0.2); }
.card i { font-size:50px; color:#3498db; margin-bottom:10px; }
.card h2 { margin:10px 0; font-size:24px; }
.card p { font-size:18px; color:#555; }
.logout { position:absolute; top:10px; right:20px; color:#fff; text-decoration:none; background:#e74c3c; padding:8px 15px; border-radius:5px; }
.logout:hover { background:#c0392b; }
</style>
</head>
<body>

<!-- Welcome Page -->
<div id="welcome">
    <h1>Welcome to Frank Sales Management System</h1>
    <p>Manage your inventories, sales, and reports efficiently</p>
    <a href="index.php?page=login">Login</a>
</div>

<!-- Login Page -->
<div id="login">
    <div class="login-container">
        <h2>Login</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p style="margin-top:10px;"><a href="index.php">Back to Welcome</a></p>
    </div>
</div>

<!-- Dashboard Page -->
<?php if($page==='dashboard'): 
// Fetch counts
$inventory = $conn->query("SELECT COUNT(*) AS total_inventory FROM car")->fetch_assoc()['total_inventory'];
$customers = $conn->query("SELECT COUNT(*) AS total_customers FROM customers")->fetch_assoc()['total_customers'];
$sales = $conn->query("SELECT COUNT(*) AS total_sales FROM sales")->fetch_assoc()['total_sales'];
$reports = $conn->query("SELECT COUNT(*) AS total_reports FROM reports")->fetch_assoc()['total_reports'] ?? 0;
?>
<div id="dashboard">
    <div class="sidebar">
        <h2>Frank Sales</h2>
        <a href="index.php?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#"><i class="fas fa-car"></i> Inventories</a>
        <a href="#"><i class="fas fa-users"></i> Customers</a>
        <a href="#"><i class="fas fa-dollar-sign"></i> Sales</a>
        <a href="#"><i class="fas fa-file-alt"></i> Reports</a>
    </div>
    <a href="index.php?logout=true" class="logout">Logout</a>
    <div class="main">
        <h1 style="text-align:center; padding-bottom:20px;">Admin Dashboard</h1>
        <div class="dashboard-cards">
            <div class="card">
                <i class="fas fa-car"></i>
                <h2>Inventories</h2>
                <p><?php echo $inventory; ?> cars</p>
            </div>
            <div class="card">
                <i class="fas fa-users"></i>
                <h2>Customers</h2>
                <p><?php echo $customers; ?> customers</p>
            </div>
            <div class="card">
                <i class="fas fa-dollar-sign"></i>
                <h2>Sales</h2>
                <p><?php echo $sales; ?> sales</p>
            </div>
            <div class="card">
                <i class="fas fa-file-alt"></i>
                <h2>Reports</h2>
                <p><?php echo $reports; ?> reports</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

</body>
</html>
