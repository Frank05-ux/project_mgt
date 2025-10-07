<?php
session_start();
include 'db.php';

// --- Summary queries ---

// Total cars
$totalCars = $conn->query("SELECT COUNT(*) AS total FROM car")->fetch_assoc()['total'];

// Sold cars (match lowercase 'sold')
$soldCars = $conn->query("SELECT COUNT(*) AS sold FROM car WHERE status='sold'")->fetch_assoc()['sold'];

// Available cars
$availableCars = $conn->query("SELECT COUNT(*) AS available FROM car WHERE status='available'")->fetch_assoc()['available'];

// Total sales value (COALESCE avoids null if no sales)
$totalSales = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total_sales FROM sales")->fetch_assoc()['total_sales'];

// Total customers
$totalCustomers = $conn->query("SELECT COUNT(*) AS total_customers FROM customers")->fetch_assoc()['total_customers'];

// Total number of unique cars sold
$uniqueCarsSold = $conn->query("SELECT COUNT(DISTINCT car_id) AS unique_sold FROM sales")->fetch_assoc()['unique_sold'];

// --- Dynamic Back Link ---
if (isset($_SESSION['role'])) {
  if ($_SESSION['role'] === 'admin') {
    $redirect = 'admin_dashboard.php';
  } elseif ($_SESSION['role'] === 'sales') {
    $redirect = 'inventory.php';
  } else {
    $redirect = 'login.php';
  }
} else {
  // fallback if role not set
  $redirect = 'login.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
  color: #fff;
  margin: 0;
  padding: 0;
}

.container {
  max-width: 1150px;
  margin: 60px auto;
  background: rgba(0,0,0,0.75);
  border-radius: 15px;
  padding: 40px 30px;
  box-shadow: 0 0 25px rgba(0,0,0,0.6);
}

h1 {
  text-align: center;
  color: #00ffcc;
  margin-bottom: 40px;
  font-size: 2.2rem;
  letter-spacing: 1px;
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 25px;
}

.card {
  background: rgba(255,255,255,0.1);
  border-radius: 15px;
  padding: 25px 15px;
  text-align: center;
  box-shadow: 0 0 12px rgba(0,0,0,0.3);
  transition: 0.3s;
}
.card:hover {
  transform: scale(1.05);
  background: rgba(255,255,255,0.15);
}

.card h2 {
  font-size: 2.3rem;
  margin: 10px 0;
  color: #00ffcc;
}

.card p {
  font-size: 1rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #ccc;
}

.back-btn {
  display: inline-block;
  margin-top: 40px;
  padding: 12px 25px;
  background: #00ffcc;
  color: #000;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  transition: 0.3s;
}

.back-btn:hover {
  background: #00d4a5;
}

footer {
  margin-top: 30px;
  text-align: center;
  color: #aaa;
  font-size: 0.9rem;
}
</style>
</head>
<body>

<div class="container">
  <h1>📊 System Reports Dashboard</h1>

  <div class="cards">
    <div class="card">
      <h2><?= $totalCars ?></h2>
      <p>Total Cars in System</p>
    </div>

    <div class="card">
      <h2><?= $soldCars ?></h2>
      <p>Sold Cars</p>
    </div>

    <div class="card">
      <h2><?= $availableCars ?></h2>
      <p>Available Cars</p>
    </div>

    <div class="card">
      <h2>$<?= number_format($totalSales, 2) ?></h2>
      <p>Total Sales Revenue</p>
    </div>

    <div class="card">
      <h2><?= $uniqueCarsSold ?></h2>
      <p>Unique Cars Sold</p>
    </div>

    <div class="card">
      <h2><?= $totalCustomers ?></h2>
      <p>Total Customers</p>
    </div>
  </div>

  <center>
    <a href="<?= $redirect ?>" class="back-btn">⬅ Back</a>
  </center>

  <footer>
    © <?= date('Y') ?> Car Sales Management System
  </footer>
</div>

</body>
</html>
