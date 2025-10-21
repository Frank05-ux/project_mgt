```php
<?php
session_start();
// Receptionist authentication
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
  header("Location: admin_login.php");
  exit();
}
include('db_connect.php');

// Handle Add Patient
if(isset($_POST['add_patient'])) {
    $name = $_POST['name'];
    $ticket_no = rand(100,999);
    $status = 'waiting';
    $stmt = $conn->prepare("INSERT INTO queue (patient_name, ticket_no, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $ticket_no, $status);
    $stmt->execute();
}

// Handle Call Next
if(isset($_POST['call_next'])) {
    $result = $conn->query("SELECT * FROM queue WHERE status='waiting' ORDER BY id ASC LIMIT 1");
    if($row = $result->fetch_assoc()) {
        $conn->query("UPDATE queue SET status='called' WHERE id=" . $row['id']);
        $called_ticket = $row['ticket_no'];
    }
}

// Fetch Queue
$queue = $conn->query("SELECT * FROM queue ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receptionist Dashboard</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
        body {
          font-family: 'Poppins', sans-serif;
          background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
          color: #222;
          margin: 0;
          padding: 0;
          min-height: 100vh;
        }
        .dashboard {
          max-width: 900px;
          margin: 60px auto;
          background: rgba(255,255,255,0.97);
          border-radius: 18px;
          padding: 38px 32px 32px 32px;
          box-shadow: 0 8px 32px rgba(112,183,255,0.13);
        }
        h1 {
          text-align: center;
          font-size: 2.2rem;
          color: #0546a0;
          margin-bottom: 18px;
          font-weight: 700;
          letter-spacing: 1px;
        }
        form {
          display: flex;
          justify-content: center;
          margin-bottom: 30px;
          gap: 10px;
          flex-wrap: wrap;
        }
        input[type=text] {
          padding: 12px 14px;
          width: 250px;
          border-radius: 10px;
          border: 1px solid #b3d1ff;
          background: #f7fbff;
          font-size: 1rem;
          color: #222;
          outline: none;
          transition: box-shadow 0.3s;
        }
        input[type=text]:focus {
          box-shadow: 0 0 8px #70b7ff;
        }
        button {
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
          margin-right: 8px;
        }
        button:last-child { margin-right: 0; }
        button:hover {
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
        .ticket-callout {
          text-align: center;
          margin: 20px 0;
          font-size: 1.3rem;
          background-color: #f7fbff;
          color: #0546a0;
          padding: 15px;
          border-radius: 10px;
          box-shadow: 0 2px 8px #b3d1ff33;
          font-weight: 600;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
          background-color: #fff;
          border-radius: 12px;
          overflow: hidden;
          box-shadow: 0 2px 10px rgba(112,183,255,0.10);
        }
        th, td {
          border-bottom: 1px solid #f0f4fa;
          padding: 13px 10px;
          text-align: center;
          color: #222;
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
        .logout {
          position: absolute;
          top: 20px;
          right: 20px;
          background: #e91e63;
          color: white;
          padding: 8px 15px;
          border-radius: 8px;
          text-decoration: none;
          font-weight: bold;
          font-size: 1rem;
          box-shadow: 0 2px 8px #e91e6333;
          transition: background 0.2s;
        }
        .logout:hover { background: #ad1457; }
        @media (max-width: 700px) {
          .dashboard { padding: 18px 6px; }
          form { flex-direction: column; gap: 12px; }
          input, button { width: 100%; margin-right: 0; }
          table, thead, tbody, th, td, tr { display: block; }
          thead tr { display: none; }
          tr { margin-bottom: 15px; border: 1px solid #eee; border-radius: 10px; padding: 10px; background: #fff; }
          td { padding: 10px 12px; text-align: right; position: relative; }
          td::before { content: attr(data-label); position: absolute; left: 12px; font-weight: 600; text-transform: capitalize; color: #2575fc; }
        }
      </style>
    </head>
    <body>
</head>
<body>

<div class="overlay"></div>
<a href="logout.php" class="logout"><i class='bx bx-log-out'></i> Logout</a>

<div class="dashboard">
  <h1>Receptionist Dashboard</h1>
  <form method="POST">
    <input type="text" name="name" placeholder="Enter Patient Name" required>
    <button type="submit" name="add_patient"><i class='bx bx-user-plus'></i> Add Patient</button>
    <button type="submit" name="call_next"><i class='bx bx-bell'></i> Call Next</button>
  </form>
  <?php if(isset($called_ticket)): ?>
    <div class="ticket-callout">
      📢 Now Calling Ticket <strong>#<?= $called_ticket ?></strong>
    </div>
  <?php endif; ?>
  <table>
    <tr>
      <th>ID</th>
      <th>Patient Name</th>
      <th>Ticket No</th>
      <th>Status</th>
    </tr>
    <?php while($row = $queue->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['patient_name']) ?></td>
        <td>#<?= $row['ticket_no'] ?></td>
        <td><?= ucfirst($row['status']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>
```
