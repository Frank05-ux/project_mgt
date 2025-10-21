<?php
session_start();

// ✅ Security: Only allow logged-in admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ✅ Database connection
$conn = new mysqli("localhost", "root", "", "hospital_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Fetch statistics safely
function getCount($conn, $query, $param = null) {
    if ($param) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $param);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    } else {
        $result = $conn->query($query);
        $count = $result->fetch_assoc()['total'] ?? 0;
    }
    return $count;
}

$totalDoctors = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE role='doctor'");
$totalPatients = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE role='patient'");
$totalAppointments = getCount($conn, "SELECT COUNT(*) AS total FROM appointments");
$totalQueue = getCount($conn, "SELECT COUNT(*) AS total FROM queue WHERE status='waiting'");
$totalServing = getCount($conn, "SELECT COUNT(*) AS total FROM queue WHERE status='serving'");
$totalNotifications = getCount($conn, "SELECT COUNT(*) AS total FROM notifications");

// ✅ Fetch appointments
$appointments = $conn->query("
    SELECT a.id, u.username AS patient, d.username AS doctor, a.appointment_date, a.appointment_time, a.status
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    JOIN users d ON a.doctor_id = d.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
    LIMIT 5
");

// ✅ Fetch notifications
$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Hospital Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}
body {
  background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
  color: #222;
  display: flex;
  min-height: 100vh;
  overflow-x: hidden;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background: linear-gradient(180deg, #4f8cff 0%, #0056b3 100%);
  height: 100vh;
  position: fixed;
  left: 0; top: 0;
  display: flex; flex-direction: column;
  justify-content: space-between;
  color: white;
  box-shadow: 2px 0 16px rgba(0,0,0,0.08);
  border-top-right-radius: 24px;
}
.sidebar h2 {
  text-align: center;
  padding: 28px 0 18px 0;
  font-weight: 700;
  color: #fff;
  font-size: 1.7rem;
  letter-spacing: 1px;
}
.nav-links { list-style: none; }
.nav-links a {
  display: flex;
  align-items: center;
  color: #eaf1ff;
  text-decoration: none;
  padding: 14px 28px;
  font-size: 1.08rem;
  border-radius: 8px 0 0 8px;
  margin-bottom: 4px;
  transition: background 0.2s, color 0.2s;
}
.nav-links a i {
  margin-right: 13px;
  font-size: 19px;
  color: #fff;
}
.nav-links a:hover, .nav-links a.active {
  background: rgba(255,255,255,0.18);
  color: #fff;
  border-left: 4px solid #fff;
}
.logout {
  text-align: center;
  padding: 15px;
  background: linear-gradient(90deg, #ff4b5c 0%, #ff7b8a 100%);
  color: white;
  text-decoration: none;
  transition: background 0.2s;
  font-weight: bold;
  border-bottom-left-radius: 18px;
  border-bottom-right-radius: 18px;
}
.logout:hover { background: #d62839; }

/* Main */
.main {
  margin-left: 250px;
  padding: 38px 32px 32px 32px;
  width: calc(100% - 250px);
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(10px);
  border-radius: 28px 0 0 28px;
  min-height: 100vh;
  box-shadow: 0 4px 24px rgba(112,183,255,0.08);
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}
.header .welcome {
  font-size: 1.45rem;
  font-weight: 600;
  color: #0056b3;
  letter-spacing: 0.5px;
}
.header img {
  width: 64px; height: 64px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #4f8cff;
  box-shadow: 0 3px 12px rgba(0,0,0,0.13);
}

/* Cards */
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
  gap: 22px;
  margin-bottom: 30px;
}
.card {
  background: linear-gradient(135deg, #f7faff 0%, #e3f0ff 100%);
  padding: 24px 18px 18px 18px;
  border-radius: 18px;
  box-shadow: 0 2px 12px rgba(112,183,255,0.10);
  text-align: center;
  transition: box-shadow 0.3s, transform 0.3s;
  transform: scale(1);
  position: relative;
  overflow: hidden;
}
.card:hover {
  transform: translateY(-6px) scale(1.04);
  box-shadow: 0 8px 24px rgba(112,183,255,0.18);
}
.card img {
  width: 54px;
  height: 54px;
  margin-bottom: 8px;
  filter: drop-shadow(0 2px 6px #b3d1ff88);
}
.card h3 {
  color: #0056b3;
  margin-bottom: 4px;
  font-size: 1.13rem;
  font-weight: 600;
}
.card p {
  font-size: 1.7rem;
  font-weight: bold;
  color: #222;
  margin-top: 2px;
}

/* Tables */
section h2 {
  margin: 28px 0 12px 0;
  color: #0056b3;
  font-size: 1.18rem;
  font-weight: 600;
}
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
.status {
  padding: 5px 14px;
  border-radius: 10px;
  color: #fff;
  font-weight: 600;
  font-size: 0.97rem;
  box-shadow: 0 1px 4px #b3d1ff33;
}
.status.pending { background: #f0ad4e; }
.status.completed { background: #28a745; }
.status.cancelled { background: #dc3545; }
tr:hover { background: #f7fbff; transition: 0.3s; }

/* Responsive */
@media(max-width:900px) {
  .sidebar { width: 180px; }
  .main { margin-left: 180px; width: calc(100% - 180px); }
}
@media(max-width:700px) {
  body { flex-direction: column; }
  .sidebar {
    width: 100%; height: auto; flex-direction: row; overflow-x: auto;
    position: static; border-radius: 0;
  }
  .main { margin-left: 0; width: 100%; border-radius: 0; }
}
</style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <h2>🛡️ Admin Dashboard</h2>
      <ul class="nav-links">
        <li><a href="admin_dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="doctors.php"><i class="fas fa-user-md"></i> Manage Doctors</a></li>
        <li><a href="manage_patient.php"><i class="fas fa-user-cog"></i> Manage Patient</a></li>
        <li><a href="manage_receptionist.php"><i class="fas fa-user-tie"></i> Manage Receptionist</a></li>
        <li><a href="queue.php"><i class="fas fa-list"></i> Queue</a></li>
        <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
        <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
      </ul>
    </div>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="header">
      <div class="welcome">👋 Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</div>
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Admin">
    </div>

    <div class="stats">
      <div class="card"><img src="https://cdn-icons-png.flaticon.com/512/3209/3209343.png"><h3>Doctors</h3><p><?= $totalDoctors ?></p></div>
      <div class="card"><img src="https://cdn-icons-png.flaticon.com/512/4320/4320337.png"><h3>Patients</h3><p><?= $totalPatients ?></p></div>
      <div class="card"><img src="https://cdn-icons-png.flaticon.com/512/706/706164.png"><h3>Appointments</h3><p><?= $totalAppointments ?></p></div>
      <div class="card"><img src="https://cdn-icons-png.flaticon.com/512/3190/3190804.png"><h3>Waiting Queue</h3><p><?= $totalQueue ?></p></div>
      <div class="card"><img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png"><h3>Now Serving</h3><p><?= $totalServing ?></p></div>
      <div class="card"><img src="https://cdn-icons-png.flaticon.com/512/3602/3602145.png"><h3>Notifications</h3><p><?= $totalNotifications ?></p></div>
    </div>

    <section>
      <h2>Recent Appointments</h2>
      <table>
        <tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
        <?php while ($a = $appointments->fetch_assoc()): ?>
        <tr>
          <td><?= $a['id'] ?></td>
          <td><?= htmlspecialchars($a['patient']) ?></td>
          <td><?= htmlspecialchars($a['doctor']) ?></td>
          <td><?= $a['appointment_date'] ?></td>
          <td><?= $a['appointment_time'] ?></td>
          <td><span class="status <?= strtolower($a['status']) ?>"><?= ucfirst($a['status']) ?></span></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </section>

    <section>
      <h2>Recent Notifications</h2>
      <table>
        <tr><th>ID</th><th>Message</th><th>Role</th><th>Date</th></tr>
        <?php while ($n = $notifications->fetch_assoc()): ?>
        <tr>
          <td><?= $n['id'] ?></td>
          <td><?= htmlspecialchars($n['message']) ?></td>
          <td><?= ucfirst($n['recipient_role']) ?></td>
          <td><?= $n['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </section>
  </div>
</body>
</html>
