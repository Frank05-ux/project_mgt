<?php
session_start();
if(!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

// Database connection
$conn = new mysqli("localhost","root","","admin_db");
if($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle new customer form
if(isset($_POST['add_customer'])){
  $name = $_POST['full_name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $message = $_POST['message'];

  $stmt = $conn->prepare("INSERT INTO customers (full_name, email, phone, message) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $phone, $message);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('✅ Customer added successfully!'); window.location='customers.php';</script>";
}

// Handle send message form
if(isset($_POST['send_message'])){
  $customer_id = $_POST['customer_id'];
  $subject = $_POST['subject'];
  $msg = $_POST['msg'];

  $stmt = $conn->prepare("INSERT INTO messages (customer_id, subject, message) VALUES (?, ?, ?)");
  $stmt->bind_param("iss", $customer_id, $subject, $msg);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('📨 Message sent successfully!'); window.location='customers.php';</script>";
}

// Fetch customers
$customers = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customers Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: url('https://images.unsplash.com/photo-1615874959474-d609969a20ed?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat fixed;
  color: #fff;
  margin: 0;
  padding: 0;
}

.container {
  max-width: 1200px;
  margin: 50px auto;
  background: rgba(0,0,0,0.85);
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 0 25px rgba(0,0,0,0.7);
}

h1 {
  text-align: center;
  color: #00ffcc;
  margin-bottom: 30px;
}

form {
  display: grid;
  gap: 15px;
  background: rgba(255,255,255,0.08);
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 40px;
}

input, textarea, select {
  padding: 12px;
  border: none;
  border-radius: 6px;
  width: 100%;
  font-size: 1rem;
}

button {
  background: #00ffcc;
  border: none;
  color: #000;
  padding: 12px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  background: #00d4a5;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  overflow: hidden;
}

th, td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #444;
}

th {
  background: rgba(0,255,204,0.2);
  color: #00ffcc;
}

tr:hover {
  background: rgba(255,255,255,0.1);
}

a.back-btn {
  display: inline-block;
  background: #00ffcc;
  color: #000;
  padding: 12px 25px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  transition: 0.3s;
}

a.back-btn:hover {
  background: #00d4a5;
}

footer {
  margin-top: 30px;
  text-align: center;
  color: #aaa;
}
</style>
</head>
<body>

<div class="container">
  <h1>👥 Customer Management Panel</h1>

  <!-- Add Customer Form -->
  <form method="POST">
    <h2>Add New Customer</h2>
    <input type="text" name="full_name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="text" name="phone" placeholder="Phone Number" required>
    <textarea name="message" placeholder="Message / Notes (optional)"></textarea>
    <button type="submit" name="add_customer">➕ Add Customer</button>
  </form>

  <!-- Send Message Form -->
  <form method="POST">
    <h2>Send Message to Customer</h2>
    <select name="customer_id" required>
      <option value="">-- Select Customer --</option>
      <?php
      $customerList = $conn->query("SELECT id, full_name FROM customers ORDER BY full_name ASC");
      while($row = $customerList->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['full_name']}</option>";
      }
      ?>
    </select>
    <input type="text" name="subject" placeholder="Message Subject" required>
    <textarea name="msg" placeholder="Type your message here..." required></textarea>
    <button type="submit" name="send_message">📨 Send Message</button>
  </form>

  <!-- Customers Table -->
  <h2>Customer List</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Message / Notes</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $customers->fetch_assoc()) { ?>
      <tr>
        <td><?= $row['id']; ?></td>
        <td><?= $row['full_name']; ?></td>
        <td><?= $row['email']; ?></td>
        <td><?= $row['phone']; ?></td>
        <td><?= $row['message']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <center style="margin-top:25px;">
    <a href="inventory.php" class="back-btn">⬅ Back to Inventory</a>
  </center>

  <footer>
    © <?= date('Y') ?> frank Sales Management System
  </footer>
</div>

</body>
</html>
