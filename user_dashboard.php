<?php
session_start();
include 'db_connect.php';
include 'auto_fix_car_images.php'; // ensure this file exists and works

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: user_login.php");
    exit();
}

// Auto-fix missing car images
auto_fix_car_images($conn);

// Get logged-in customer info
$customer_id = $_SESSION['customer_id'];
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Handle purchases
$msg = '';
if (isset($_GET['buy'])) {
    $car_id = intval($_GET['buy']);
    if ($conn->query("INSERT INTO sales (car_id, customer_id, quantity) VALUES ($car_id, $customer_id, 1)")) {
        $msg = "✅ Purchase successful! Thank you.";
    } else {
        $msg = "❌ Error processing purchase.";
    }
}

// Get user's purchases
$purchases = $conn->query("
    SELECT s.id AS sale_id, c.name, c.model, c.year, c.price, c.image, s.amount, s.sale_date
    FROM sales s
    LEFT JOIN car c ON s.car_id = c.id
    WHERE s.customer_id = $customer_id
    ORDER BY s.sale_date DESC
");

// Get available cars
$cars = $conn->query("SELECT * FROM car WHERE status='available' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>
<style>
/* ----------------------- Global Styles ----------------------- */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    color: #fff;
    min-height: 100vh;
    /* Professional car background */
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                url('images/cars/dashboard_bg_new.jpeg') center/cover no-repeat fixed;
}

/* ----------------------- Top Bar ----------------------- */
.top-bar {
    text-align: center;
    padding: 15px;
    background: rgba(0,0,0,0.7);
    position: sticky;
    top: 0;
    z-index: 100;
}
.top-bar a {
    background:#2196F3;
    color:#fff;
    padding:10px 15px;
    text-decoration:none;
    border-radius:8px;
    margin:0 5px;
    transition: 0.3s;
}
.top-bar a:hover { 
    background:#1976D2; 
}

/* ----------------------- Container ----------------------- */
.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: rgba(0,0,0,0.7);
    border-radius: 10px;
}

/* ----------------------- Headings ----------------------- */
h2 { border-bottom: 2px solid #fff; padding-bottom:5px; margin-bottom:15px; }

/* ----------------------- Profile ----------------------- */
.profile p span { font-weight:bold; }

/* ----------------------- Purchases Table ----------------------- */
.purchases table {
    width: 100%;
    border-collapse: collapse;
    color: #fff;
}
.purchases th, .purchases td {
    padding: 8px;
    border: 1px solid #555;
    text-align: center;
}
.purchases th { background: #3b4252; }

/* ----------------------- Available Cars ----------------------- */
.available-cars {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px,1fr));
    gap: 20px;
}
.available-cars .car {
    background: rgba(0,0,0,0.8);
    border-radius: 10px;
    overflow: hidden;
    text-align: center;
    padding: 10px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.available-cars .car:hover {
    transform: scale(1.03);
    box-shadow: 0 0 15px #2196F3;
}
.available-cars .car img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius:5px;
}
.available-cars .car h3 { margin:10px 0; }
.available-cars .car p { margin:5px 0; }
.available-cars button {
    background: #4CAF50;
    color: #fff;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}
.available-cars button:hover { background: #45a049; }

/* ----------------------- Message ----------------------- */
.msg {
    text-align:center;
    margin-bottom:15px;
    font-size: 18px;
    color: #ffeb3b;
}
</style>
</head>
<body>

<div class="top-bar">
    <a href="user_dashboard.php">🏠 Dashboard</a>
    <a href="user_cars.php">🚗 Buy Cars</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>👤 Welcome, <?= htmlspecialchars($user['full_name'] ?? 'User') ?></h2>

    <?php if(!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

    <!-- Profile -->
    <div class="profile">
        <h3>Your Profile</h3>
        <p>Name: <span><?= htmlspecialchars($user['full_name'] ?? '-') ?></span></p>
        <p>Email: <span><?= htmlspecialchars($user['email'] ?? '-') ?></span></p>
        <p>Phone: <span><?= htmlspecialchars($user['phone'] ?? '-') ?></span></p>
        <p>Member Since: <span><?= htmlspecialchars(date("F d, Y", strtotime($user['created_at'] ?? date("Y-m-d")))) ?></span></p>
    </div>

    <!-- Purchases -->
    <div class="purchases">
        <h3>Your Purchases</h3>
        <?php if($purchases && $purchases->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Car</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Price</th>
                    <th>Amount Paid</th>
                    <th>Date</th>
                </tr>
                <?php while($row = $purchases->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['model']) ?></td>
                        <td><?= htmlspecialchars($row['year']) ?></td>
                        <td>$<?= number_format($row['price'],2) ?></td>
                        <td>$<?= number_format($row['amount'],2) ?></td>
                        <td><?= htmlspecialchars($row['sale_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You haven’t made any purchases yet.</p>
        <?php endif; ?>
    </div>

    <!-- Available Cars -->
    <div class="available-cars">
        <h3>Available Cars</h3>
        <?php if($cars && $cars->num_rows > 0): ?>
            <?php while($car = $cars->fetch_assoc()): ?>
                <div class="car">
                    <img src="<?= htmlspecialchars($car['image'] ?: 'images/cars/no_image.jpeg') ?>" alt="<?= htmlspecialchars($car['name']) ?>">
                    <h3><?= htmlspecialchars($car['name'] . " (" . $car['model'] . ")") ?></h3>
                    <p>Year: <?= htmlspecialchars($car['year']) ?></p>
                    <p><strong>Price: $<?= number_format($car['price'],2) ?></strong></p>
                    <form method="GET">
                        <input type="hidden" name="buy" value="<?= $car['id'] ?>">
                        <button type="submit">Buy Now</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No cars available at the moment.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
