<?php
session_start();
include 'db_connect.php';

$loggedIn = isset($_SESSION['admin']);
$page = $_GET['page'] ?? ($loggedIn ? 'dashboard' : 'welcome');

if (!$loggedIn && $page !== 'welcome') {
    header("Location: admin_login.php");
    exit();
}

// Stats (if logged in)
if ($loggedIn) {
    $inventory = $conn->query("SELECT COUNT(*) AS total FROM car")->fetch_assoc()['total'] ?? 0;
    $customers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'] ?? 0;
    $sales = $conn->query("SELECT COUNT(*) AS total FROM sales")->fetch_assoc()['total'] ?? 0;
    $total_sales = $conn->query("SELECT IFNULL(SUM(amount),0) AS total FROM sales")->fetch_assoc()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Frank Car Sales | Admin Dashboard</title>
<style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
    body{color:#fff;font-size:16px;overflow:hidden;}

    /* WELCOME */
    #welcome{
        display:<?php echo $page==='welcome'?'flex':'none'; ?>;
        flex-direction:column;align-items:center;justify-content:center;
        height:100vh;text-align:center;
        background:url('https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
    }
    #welcome h1{font-size:48px;color:#f1c40f;margin-bottom:20px;}
    #welcome p{font-size:20px;margin-bottom:30px;}
    #welcome a{background:#f1c40f;color:#000;padding:15px 30px;border-radius:8px;text-decoration:none;font-weight:bold;}
    #welcome a:hover{background:#d4ac0d;}

    /* DASHBOARD */
    #dashboard{
        display:<?php echo $page==='dashboard' && $loggedIn?'flex':'none'; ?>;
        height:100vh;background:url('https://images.unsplash.com/photo-1517142089942-ba376ce32a0a?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
    }
    .sidebar{
        width:250px;background:rgba(0,0,0,0.85);
        display:flex;flex-direction:column;padding:20px;
    }
    .sidebar h2{text-align:center;color:#f1c40f;margin-bottom:25px;}
    .sidebar a{
        color:#ddd;padding:12px 15px;text-decoration:none;margin-bottom:10px;
        border-radius:6px;transition:0.3s;
    }
    .sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.1);color:#f1c40f;}
    .logout{background:#e74c3c;text-align:center;padding:10px 0;border-radius:6px;margin-top:auto;}
    .logout:hover{background:#c0392b;}
    .content{flex:1;padding:25px;background:rgba(0,0,0,0.6);overflow-y:auto;}
    header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    header h1{color:#f1c40f;}
    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;}
    .card{background:rgba(255,255,255,0.9);color:#333;padding:25px;border-radius:10px;text-align:center;}
    .card h3{margin-bottom:10px;}
    .card p{font-size:22px;font-weight:bold;}
</style>
</head>
<body>

<!-- WELCOME -->
<section id="welcome">
    <h1>🚗 Welcome to Frank Car Sales</h1>
    <p>Manage your cars, customers, and sales all in one place.</p>
    <a href="admin_login.php">Admin Login</a>
</section>

<!-- DASHBOARD -->
<?php if($loggedIn): ?>
<section id="dashboard">
    <div class="sidebar">
        <h2>Frank Sales</h2>
        <a href="#" class="active">🏠 Dashboard</a>
        <a href="inventory.php">🚗 Inventories</a>
        <a href="customers.php">👥 Customers</a>
        <a href="sales.php">💰 Sales</a>
        <a href="reports.php">📊 Reports</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
    <div class="content">
        <header><h1>Welcome, <?php echo $_SESSION['admin']; ?> 👋</h1></header>
        <div class="stats">
            <div class="card"><h3>Total Inventory</h3><p><?php echo $inventory; ?></p></div>
            <div class="card"><h3>Total Customers</h3><p><?php echo $customers; ?></p></div>
            <div class="card"><h3>Total Sales</h3><p><?php echo $sales; ?></p></div>
            <div class="card"><h3>Sales Revenue</h3><p>KSh <?php echo number_format($total_sales); ?></p></div>
        </div>
    </div>
</section>
<?php endif; ?>

</body>
</html>
