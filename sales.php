<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: admin_login.php");

$conn = new mysqli("localhost", "root", "", "admin_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "
SELECT 
    s.id,
    c.name AS car_name,
    c.model AS model,
    c.price AS price,
    cu.full_name AS customer_name,
    s.sale_date,
    s.amount
FROM sales s
LEFT JOIN car c ON s.car_id = c.id
LEFT JOIN customers cu ON s.customer_id = cu.id
ORDER BY s.id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Car Sales</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: url('https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
    color: #fff;
    margin: 0;
    padding: 0;
}
.container {
    background: rgba(0,0,0,0.75);
    max-width: 1000px;
    margin: 60px auto;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.9);
}
h1 {
    text-align: center;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 2px;
}
table {
    width: 100%;
    border-collapse: collapse;
    color: #fff;
}
th, td {
    padding: 12px;
    border: 1px solid #555;
    text-align: center;
}
th {
    background: rgba(0, 0, 0, 0.85);
    font-weight: bold;
}
tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.1);
}
tr:hover {
    background: rgba(255, 255, 255, 0.2);
}
.top-buttons {
    margin-bottom: 20px;
    text-align: center;
}
.top-buttons a {
    padding: 10px 15px;
    background: #2563eb;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    margin: 0 6px;
    transition: 0.3s;
}
.top-buttons a:hover {
    background: #1e40af;
}
</style>
</head>
<body>
<div class="container">
<h1>Sales Report</h1>

<div class="top-buttons">
    <a href="add_sale.php">+ Record Sale</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</div>

<table>
<thead>
<tr>
<th>ID</th>
<th>Car Name</th>
<th>Model</th>
<th>Price</th>
<th>Customer</th>
<th>Amount Paid</th>
<th>Sale Date</th>
</tr>
</thead>
<tbody>
<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['car_name'] ?? 'Unknown') ?></td>
        <td><?= htmlspecialchars($row['model'] ?? '-') ?></td>
        <td>$<?= number_format($row['price'], 2) ?></td>
        <td><?= htmlspecialchars($row['customer_name'] ?? 'Unknown') ?></td>
        <td>$<?= number_format($row['amount'], 2) ?></td>
        <td><?= htmlspecialchars($row['sale_date']) ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="7">No sales recorded</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</body>
</html>
<?php $conn->close(); ?>
