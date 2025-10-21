<?php
session_start();

// ✅ Ensure only logged-in admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ✅ Connect to database
$conn = new mysqli("localhost", "root", "", "hospital_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Handle notification submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $recipient_role = 'all'; // optional: can target specific roles later

    $stmt = $conn->prepare("INSERT INTO notifications (message, recipient_role) VALUES (?, 'admin')");
    $stmt->bind_param("s", $message);
    $stmt->execute();
    $stmt->close();

    $success = "✅ Notification sent successfully!";
}

// ✅ Fetch notifications (latest first)
$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Notifications | Hospital Management System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous"/>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('https://images.unsplash.com/photo-1576765607924-3aa6b79b5b9c?auto=format&fit=crop&w=1400&q=80') no-repeat center center/cover;
      margin: 0; padding: 0;
      color: #333;
      backdrop-filter: blur(6px);
    }
    header {
      background: rgba(37,117,252,0.9);
      color: white;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header a {
      color: white;
      background: rgba(255,255,255,0.2);
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      transition: 0.3s;
    }
    header a:hover { background: rgba(255,255,255,0.3); }
    .container {
      background: rgba(255,255,255,0.95);
      margin: 40px auto;
      padding: 30px;
      border-radius: 12px;
      width: 75%;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    form textarea, button {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin: 10px 0;
      font-size: 15px;
      resize: none;
    }
    button {
      background: #2575fc;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
    }
    button:hover { background: #1a5dd1; }

    .alert {
      padding: 10px 15px;
      background: #d4edda;
      color: #155724;
      border-radius: 6px;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
      background: white;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 14px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #2575fc;
      color: white;
    }
    tr:hover {
      background: #f1f5ff;
    }
  </style>
</head>
<body>

  <header>
    <h2><i class="fa-solid fa-bell"></i> Notifications</h2>
    <a href="admin_dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
  </header>

  <div class="container">
    <h3><i class="fa-solid fa-paper-plane"></i> Send Notification</h3>

    <?php if (!empty($success)): ?>
      <div class="alert"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <textarea name="message" rows="5" placeholder="Write a system notification here..." required></textarea>
      <button type="submit"><i class="fa-solid fa-paper-plane"></i> Send Notification</button>
    </form>

    <h3 style="margin-top:40px;"><i class="fa-solid fa-bell"></i> Recent Notifications</h3>
    <table>
      <tr>
        <th>ID</th>
        <th>Message</th>
        <th>Recipient Role</th>
        <th>Date</th>
      </tr>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['message']) ?></td>
            <td><?= ucfirst($row['recipient_role']) ?></td>
            <td><?= $row['created_at'] ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">No notifications yet.</td></tr>
      <?php endif; ?>
    </table>
  </div>

</body>
</html>
