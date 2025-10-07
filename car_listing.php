<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['customer_id'])) {
  header("Location: user_login.php");
  exit();
}

if (isset($_GET['buy'])) {
  $car_id = $_GET['buy'];
  $customer_id = $_SESSION['customer_id'];
  $conn->query("INSERT INTO sales (car_id, customer_id, quantity) VALUES ($car_id, $customer_id, 1)");
  $msg = "✅ Purchase successful! Thank you.";
}

$cars = $conn->query("SELECT * FROM car WHERE status='available'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Available Cars</title>
<style>
body {
  font-family: Arial;
  background: url('https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
  color: #fff;
}
.container {
  width: 90%;
  margin: 40px auto;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
.car {
  background: rgba(0,0,0,0.7);
  padding: 15px;
  border-radius: 10px;
  text-align: center;
}
button {
  background: #4CAF50;
  color: #fff;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
}
button:hover { background: #45a049; }
.msg { text-align:center; margin-top:15px; }
</style>
</head>
<body>
<h1 style="text-align:center;">🚗 Available Cars</h1>
<?php if(!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>
<div class="container">
<?php while($car = $cars->fetch_assoc()): ?>
  <div class="car">
    <h3><?= $car['name'] . " (" . $car['model'] . ")" ?></h3>
    <p>Year: <?= $car['year'] ?></p>
    <p>Price: $<?= $car['price'] ?></p>
    <form method="GET">
      <input type="hidden" name="buy" value="<?= $car['id'] ?>">
      <button type="submit">Buy Now</button>
    </form>
  </div>
<?php endwhile; ?>
</div>
</body>
</html>
