<?php
session_start();

// ✅ Ensure only logged-in admins or receptionists can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'receptionist'])) {
    header("Location: admin_login.php");
    exit();
}

// ✅ Connect to database
$conn = new mysqli("localhost", "root", "", "hospital_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Handle queue actions: serve / call / done / remove
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'serve') {
        // Set all serving to waiting first
        $conn->query("UPDATE queue SET status='waiting' WHERE status='serving'");
        $conn->query("UPDATE queue SET status='serving' WHERE id=$id");
    } elseif ($action === 'call') {
        $conn->query("UPDATE queue SET status='called' WHERE id=$id");
    } elseif ($action === 'done') {
        $conn->query("UPDATE queue SET status='completed' WHERE id=$id");
    } elseif ($action === 'remove') {
        $conn->query("DELETE FROM queue WHERE id=$id");
    }

    header("Location: queue.php");
    exit();
}

// ✅ Add to queue
if (isset($_POST['add_queue'])) {
    $patient_name = trim($_POST['patient_name']);

    if (!empty($patient_name)) {
        // Get next ticket number
        $lastTicket = $conn->query("SELECT ticket_no FROM queue ORDER BY ticket_no DESC LIMIT 1")->fetch_assoc();
        $nextNo = $lastTicket ? $lastTicket['ticket_no'] + 1 : 1;
        $ticket_code = 'Q' . str_pad($nextNo, 4, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("INSERT INTO queue (patient_name, ticket_no, ticket_number, status) VALUES (?, ?, ?, 'waiting')");
        $stmt->bind_param("sis", $patient_name, $nextNo, $ticket_code);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: queue.php");
    exit();
}

// ✅ Fetch queues
$current = $conn->query("SELECT * FROM queue WHERE status='serving' LIMIT 1")->fetch_assoc();
$waiting = $conn->query("SELECT * FROM queue WHERE status='waiting' ORDER BY created_at ASC");
$called = $conn->query("SELECT * FROM queue WHERE status='called' ORDER BY created_at ASC");
$completed = $conn->query("SELECT * FROM queue WHERE status='completed' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Queue Management | Hospital System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body {
  font-family: 'Inter', sans-serif;
  background: url('https://images.unsplash.com/photo-1584467735815-f778f274e296?auto=format&fit=crop&w=1400&q=80') no-repeat center/cover;
  margin: 0; padding: 0; color: #333; backdrop-filter: blur(5px);
}
header {
  background: rgba(37,117,252,0.9); color: white;
  padding: 20px 40px; display: flex; justify-content: space-between; align-items: center;
}
header a {
  color: white; background: rgba(255,255,255,0.2); padding: 10px 18px; border-radius: 6px; text-decoration: none;
}
.container {
  width: 85%; margin: 40px auto; background: rgba(255,255,255,0.95);
  padding: 30px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
h3 { text-align: center; margin-bottom: 25px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 10px; overflow: hidden; }
th, td { padding: 12px 14px; border-bottom: 1px solid #ddd; text-align: left; }
th { background: #2575fc; color: white; }
tr:hover { background: #f1f5ff; }
.btn {
  padding: 6px 12px; border-radius: 6px; text-decoration: none; color: white;
  font-size: 13px; transition: 0.2s; margin-right: 4px;
}
.serve { background: #28a745; }
.call { background: #17a2b8; }
.done { background: #6f42c1; }
.remove { background: #dc3545; }
.serve:hover { background: #218838; }
.call:hover { background: #138496; }
.done:hover { background: #5936a2; }
.remove:hover { background: #c82333; }
.add-form {
  text-align: center; margin-bottom: 25px;
}
input[type="text"] {
  padding: 8px 12px; border: 1px solid #ccc; border-radius: 8px; width: 300px;
}
button {
  background: #2575fc; color: white; border: none; padding: 8px 14px; border-radius: 8px;
  cursor: pointer; font-weight: 600;
}
button:hover { background: #1a5dd1; }
.now-serving {
  background: #e8f0ff; padding: 20px; border-radius: 10px; text-align: center;
  font-size: 20px; margin-bottom: 25px;
}
</style>
</head>
<body>

<header>
  <h2><i class="fa-solid fa-people-group"></i> Queue Management</h2>
  <a href="admin_dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
</header>

<div class="container">
  <h3><i class="fa-solid fa-list"></i> Live Queue System</h3>

  <div class="now-serving">
    <?php if ($current): ?>
      <strong>Now Serving:</strong> <?= htmlspecialchars($current['patient_name']) ?> (<?= $current['ticket_number'] ?>)
    <?php else: ?>
      <strong>No one is being served right now.</strong>
    <?php endif; ?>
  </div>

  <div class="add-form">
    <form method="POST">
      <input type="text" name="patient_name" placeholder="Enter patient name..." required>
      <button type="submit" name="add_queue"><i class="fa-solid fa-plus"></i> Add to Queue</button>
    </form>
  </div>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Ticket</th>
      <th>Status</th>
      <th>Created</th>
      <th>Actions</th>
    </tr>
    <?php if ($waiting->num_rows > 0): ?>
      <?php while ($row = $waiting->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['patient_name']) ?></td>
        <td><?= $row['ticket_number'] ?></td>
        <td><?= ucfirst($row['status']) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <a href="?action=serve&id=<?= $row['id'] ?>" class="btn serve">Serve</a>
          <a href="?action=call&id=<?= $row['id'] ?>" class="btn call">Call</a>
          <a href="?action=remove&id=<?= $row['id'] ?>" class="btn remove">Remove</a>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" style="text-align:center;">No one waiting.</td></tr>
    <?php endif; ?>
  </table>

  <h3 style="margin-top:40px;">Called / Completed</h3>
  <table>
    <tr><th>ID</th><th>Name</th><th>Ticket</th><th>Status</th><th>Created</th><th>Action</th></tr>
    <?php
    $merged = $conn->query("SELECT * FROM queue WHERE status IN ('called','completed') ORDER BY created_at DESC");
    if ($merged->num_rows > 0):
        while ($row = $merged->fetch_assoc()):
    ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['patient_name']) ?></td>
        <td><?= $row['ticket_number'] ?></td>
        <td><?= ucfirst($row['status']) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <?php if ($row['status'] === 'called'): ?>
            <a href="?action=done&id=<?= $row['id'] ?>" class="btn done">Mark Done</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="6" style="text-align:center;">No called/completed entries.</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>
