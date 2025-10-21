<?php
session_start();
// Only allow admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'hospital_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$success = $error = '';
// Handle Add Receptionist
if (isset($_POST['add_receptionist'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'receptionist')");
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
        $success = "Receptionist added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}
// Handle Delete Receptionist
if (isset($_POST['delete_receptionist'])) {
    $id = intval($_POST['receptionist_id']);
    if ($conn->query("DELETE FROM users WHERE id=$id AND role='receptionist'")) {
        $success = "Receptionist deleted successfully!";
    } else {
        $error = "Error deleting receptionist.";
    }
}
// Fetch all receptionists
$result = $conn->query("SELECT * FROM users WHERE role='receptionist' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Receptionists | Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
  margin: 0;
  color: #222;
}
.container {
  max-width: 900px;
  margin: 50px auto;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(112,183,255,0.13);
  padding: 38px 32px 32px 32px;
}
h2 {
  color: #0546a0;
  font-size: 2rem;
  margin-bottom: 18px;
  font-weight: 700;
  letter-spacing: 1px;
}
form {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 30px;
  align-items: flex-end;
}
form input {
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #b3d1ff;
  background: #f7fbff;
  font-size: 1rem;
  color: #222;
  outline: none;
  transition: box-shadow 0.3s;
}
form input:focus {
  box-shadow: 0 0 8px #70b7ff;
}
form button {
  padding: 13px 22px;
  border: none;
  background: linear-gradient(90deg, #0546a0 0%, #70b7ff 100%);
  color: #fff;
  font-weight: 600;
  border-radius: 10px;
  font-size: 1.08rem;
  box-shadow: 0 2px 8px rgba(112,183,255,0.10);
  transition: background 0.3s, transform 0.3s;
  cursor: pointer;
}
form button:hover {
  background: linear-gradient(90deg, #70b7ff 0%, #0546a0 100%);
  transform: scale(1.04);
}
.success, .error {
  margin-bottom: 18px;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
}
.success { background: #eaffea; color: #28a745; border-left: 4px solid #28a745; }
.error { background: #ffd2d2; color: #d8000c; border-left: 4px solid #d8000c; }
table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(112,183,255,0.10);
  margin-bottom: 18px;
}
th, td {
  padding: 13px 10px;
  text-align: left;
  border-bottom: 1px solid #f0f4fa;
  font-size: 1rem;
}
th {
  background: linear-gradient(90deg, #4f8cff 0%, #70b7ff 100%);
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.98rem;
}
tr:nth-child(even) { background: #f7fbff; }
tr:hover { background: #eaf1ff; transition: 0.2s; }
.delete-btn {
  background: #ff4b5c;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.delete-btn:hover { background: #d62839; }
</style>
</head>
<body>
<div class="container">
  <a href="admin_dashboard.php" style="display:inline-block; margin-bottom:18px; background:linear-gradient(90deg,#0546a0 0%,#70b7ff 100%); color:#fff; padding:12px 22px; border-radius:10px; text-decoration:none; font-weight:600; box-shadow:0 2px 8px #b3d1ff33;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  <h2><i class="fas fa-user-tie"></i> Manage Receptionists</h2>
  <?php if($success) echo "<div class='success'>$success</div>"; ?>
  <?php if($error) echo "<div class='error'>$error</div>"; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="add_receptionist"><i class="fas fa-plus"></i> Add Receptionist</button>
  </form>
  <table>
    <thead>
      <tr><th>ID</th><th>Username</th><th>Email</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="receptionist_id" value="<?= $row['id'] ?>">
            <button type="submit" name="delete_receptionist" class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    <?php if($result->num_rows == 0): ?>
      <tr><td colspan="4" style="text-align:center;">No receptionists found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
