<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");

$conn = new mysqli("localhost","root","","admin_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM car WHERE id=$id");
    header("Location: inventory.php?deleted=success");
    exit();
}
if(isset($_GET['sold'])){
    $id = intval($_GET['sold']);
    $conn->query("UPDATE car SET status='Sold' WHERE id=$id");
    header("Location: inventory.php?sold=success");
    exit();
}

$result = $conn->query("SELECT * FROM car ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Car Inventory</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: 
    linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.85)),
    url('https://images.unsplash.com/photo-1622015663319-fd1e03b9b81f?auto=format&fit=crop&w=1950&q=80') 
    center/cover no-repeat fixed;
  color: #fff;
  margin: 0;
  padding: 0;
}

.container {
  max-width: 1000px;
  margin: 60px auto;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 0 25px rgba(0,0,0,0.7);
}

h1 {
  text-align: center;
  color: #00ffcc;
  margin-bottom: 30px;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
  overflow: hidden;
  background: rgba(255,255,255,0.05);
}

th, td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #444;
}

th {
  background: rgba(0,255,204,0.2);
  color: #00ffcc;
  font-weight: bold;
}

tr:nth-child(even) {
  background: rgba(255,255,255,0.07);
}

tr:hover {
  background: rgba(255,255,255,0.1);
}

a.button {
  padding: 6px 12px;
  border-radius: 5px;
  color: #fff;
  text-decoration: none;
  font-weight: bold;
}

a.edit { background: #5e81ac; }
a.sold { background: #a3be8c; }
a.delete { background: #bf616a; }

.message {
  padding: 10px;
  background: #2e7d32;
  margin-bottom: 15px;
  border-radius: 8px;
  text-align: center;
}

.top-buttons {
  margin-bottom: 20px;
  text-align: center;
}

.top-buttons a {
  display: inline-block;
  padding: 10px 18px;
  margin: 5px;
  background: #00bfff;
  color: #fff;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  transition: 0.3s;
}

.top-buttons a:hover {
  background: #0099cc;
}

footer {
  text-align: center;
  color: #aaa;
  margin-top: 25px;
  font-size: 0.9rem;
}
</style>
</head>
<body>
<div class="container">
<h1>🏎️ Porsche Car Inventory</h1>

<?php
if(isset($_GET['added'])) echo '<div class="message">✅ Car added successfully!</div>';
if(isset($_GET['deleted'])) echo '<div class="message">✅ Car deleted successfully!</div>';
if(isset($_GET['sold'])) echo '<div class="message">✅ Car marked as sold!</div>';
?>

<div class="top-buttons">
<a href="add_car.php">➕ Add Car</a>
<a href="customers.php">👥 Customers</a>
<a href="dashboard.php">📊 Dashboard</a>
<a href="logout.php">🚪 Logout</a>
</div>

<table>
<thead>
<tr>
<th>ID</th><th>Name</th><th>Model</th><th>Price</th><th>Status</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php if($result->num_rows>0): ?>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['model']) ?></td>
<td>$<?= number_format($row['price'],2) ?></td>
<td><?= htmlspecialchars($row['status'] ?? 'Available') ?></td>
<td>
<a class="button edit" href="edit_car.php?id=<?= $row['id'] ?>">Edit</a>
<a class="button sold" href="inventory.php?sold=<?= $row['id'] ?>">Mark Sold</a>
<a class="button delete" href="inventory.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this car?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="6">🚘 No cars in inventory yet.</td></tr>
<?php endif; ?>
</tbody>
</table>

<footer>
  © <?= date('Y') ?> Porsche Car Inventory System
</footer>
</div>
</body>
</html>
<?php $conn->close(); ?>
