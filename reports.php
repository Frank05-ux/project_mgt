<?php
session_start();

// ✅ Only allow logged-in admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Connect to Database
$conn = new mysqli("localhost", "root", "", "hospital_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ================================
//  1️⃣ DAILY QUEUE SUMMARY
// ================================
$dailySql = "
    SELECT 
        DATE(created_at) AS date,
        COUNT(*) AS total_patients,
        SUM(CASE WHEN status='waiting' THEN 1 ELSE 0 END) AS waiting,
        SUM(CASE WHEN status IN ('completed','done','serving') THEN 1 ELSE 0 END) AS served
    FROM queue
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) DESC
";
$dailyResult = $conn->query($dailySql);

// ================================
//  2️⃣ DOCTOR PERFORMANCE
// ================================
$doctorSql = "
    SELECT 
        u.username AS doctor_name,
        COUNT(a.id) AS total_appointments,
        SUM(CASE WHEN a.status='completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN a.status='cancelled' THEN 1 ELSE 0 END) AS cancelled
    FROM users u
    LEFT JOIN appointments a ON u.id = a.doctor_id
    WHERE u.role='doctor'
    GROUP BY u.username
    ORDER BY total_appointments DESC
";
$doctorResult = $conn->query($doctorSql);

// ================================
//  3️⃣ PATIENT QUEUE HISTORY
// ================================
$queueSql = "
    SELECT 
        patient_name,
        status,
        created_at
    FROM queue
    ORDER BY created_at DESC
";
$queueResult = $conn->query($queueSql);

// ================================
//  4️⃣ SYSTEM STATS
// ================================
$totalDoctors = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='doctor'")->fetch_assoc()['total'];
$totalReceptionists = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='receptionist'")->fetch_assoc()['total'];
$totalPatients = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='patient'")->fetch_assoc()['total'];
$totalAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Hospital Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { width: 250px; height: 100vh; position: fixed; background: #0d6efd; color: #fff; padding-top: 20px; }
        .sidebar a { color: #fff; display: block; padding: 10px 20px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #0b5ed7; }
        .content { margin-left: 260px; padding: 20px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        table { background: #fff; border-radius: 8px; overflow: hidden; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_patient.php"><i class="fas fa-user-injured"></i> Manage Patients</a>
    <a href="doctors.php"><i class="fas fa-user-md"></i> Manage Doctors</a>
    <a href="queue.php"><i class="fas fa-list"></i> Queue</a>
    <a href="reports.php" class="active"><i class="fas fa-file-alt"></i> Reports</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Content -->
<div class="content">
    <h2 class="mb-4">📊 Reports Overview</h2>

    <!-- Top Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card p-3 text-center bg-light">
                <h5>Doctors</h5>
                <h3><?= $totalDoctors ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center bg-light">
                <h5>Receptionists</h5>
                <h3><?= $totalReceptionists ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center bg-light">
                <h5>Patients</h5>
                <h3><?= $totalPatients ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center bg-light">
                <h5>Appointments</h5>
                <h3><?= $totalAppointments ?></h3>
            </div>
        </div>
    </div>

    <!-- Daily Queue Summary -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">📅 Daily Queue Summary</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Date</th>
                        <th>Total Patients</th>
                        <th>Waiting</th>
                        <th>Served / Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $dailyResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= $row['total_patients'] ?></td>
                            <td><?= $row['waiting'] ?></td>
                            <td><?= $row['served'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Doctor Performance -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">🩺 Doctor Performance</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Doctor</th>
                        <th>Total Appointments</th>
                        <th>Completed</th>
                        <th>Cancelled</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $doctorResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                            <td><?= $row['total_appointments'] ?></td>
                            <td><?= $row['completed'] ?></td>
                            <td><?= $row['cancelled'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Patient Queue History -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">🧾 Patient Queue History</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-warning">
                    <tr>
                        <th>Patient Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $queueResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['patient_name']) ?></td>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td><?= $row['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
