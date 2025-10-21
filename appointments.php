<?php
session_start();

// ✅ Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ✅ Database connection
$conn = new mysqli("localhost", "root", "", "hospital_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Add a patient to queue
if (isset($_POST['add_queue'])) {
    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $doctor_id = intval($_POST['doctor_id']);
    
    // Get next ticket number
    $ticket_no = $conn->query("SELECT IFNULL(MAX(ticket_no),0)+1 AS next_ticket FROM queue")->fetch_assoc()['next_ticket'];
    $ticket_number = 'Q'.str_pad($ticket_no,4,'0',STR_PAD_LEFT);

    $conn->query("INSERT INTO queue (patient_name, ticket_no, ticket_number, doctor_id, status) 
                  VALUES ('$patient_name', $ticket_no, '$ticket_number', $doctor_id, 'waiting')");
    header("Location: queue.php");
    exit();
}

// ✅ Update status: serve / done
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'serve') {
        $conn->query("UPDATE queue SET status='serving' WHERE id=$id");
    } elseif ($action === 'done') {
        $conn->query("UPDATE queue SET status='done' WHERE id=$id");
    }

    header("Location: queue.php");
    exit();
}

// ✅ Fetch queue
$nowServing = $conn->query("SELECT * FROM queue WHERE status='serving' ORDER BY created_at ASC");
$waitingList = $conn->query("SELECT * FROM queue WHERE status='waiting' ORDER BY created_at ASC");

// ✅ Fetch doctors for add to queue form
$doctors = $conn->query("SELECT id, username FROM users WHERE role='doctor'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Queue Management | Hospital Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family: 'Inter', sans-serif; background:#f4f6f8; margin:0; padding:0; }
header { background:#2575fc; color:white; padding:20px 40px; display:flex; justify-content:space-between; align-items:center; }
header a { color:white; text-decoration:none; background:rgba(255,255,255,0.2); padding:8px 14px; border-radius:6px; transition:0.3s;}
header a:hover { background:rgba(255,255,255,0.3);}
.container { width:90%; margin:30px auto; background:white; padding:20px 30px; border-radius:10px; box-shadow:0 6px 15px rgba(0,0,0,0.1); }
h2,h3 { margin-bottom:20px; }
table { width:100%; border-collapse:collapse; margin-bottom:20px; }
th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
th { background:#2575fc; color:white; }
tr:hover { background:#f1f5ff; }
.btn { padding:6px 12px; border-radius:6px; text-decoration:none; color:white; font-size:13px; transition:0.2s; }
.serve { background:#28a745; } .serve:hover { background:#218838; }
.done { background:#dc3545; } .done:hover { background:#c82333; }
form input, form select, form button { padding:8px 12px; margin:5px 0; border-radius:6px; border:1px solid #ccc; }
form button { background:#2575fc; color:white; border:none; cursor:pointer; }
form button:hover { background:#1a5dd1; }
</style>
</head>
<body>

<header>
    <h2><i class="fa-solid fa-list"></i> Queue Management</h2>
    <a href="admin_dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
</header>

<div class="container">
    <h3>Now Serving</h3>
    <table>
        <tr><th>Ticket</th><th>Patient</th><th>Doctor</th><th>Status</th><th>Action</th></tr>
        <?php if ($nowServing->num_rows > 0): ?>
            <?php while($row = $nowServing->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ticket_number'] ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= $row['doctor_id'] ? 'Doctor ID '.$row['doctor_id'] : 'N/A' ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><a href="?action=done&id=<?= $row['id'] ?>" class="btn done">Done</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No patient is being served.</td></tr>
        <?php endif; ?>
    </table>

    <h3>Waiting List</h3>
    <table>
        <tr><th>Ticket</th><th>Patient</th><th>Doctor</th><th>Status</th><th>Action</th></tr>
        <?php if ($waitingList->num_rows > 0): ?>
            <?php while($row = $waitingList->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ticket_number'] ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= $row['doctor_id'] ? 'Doctor ID '.$row['doctor_id'] : 'N/A' ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><a href="?action=serve&id=<?= $row['id'] ?>" class="btn serve">Serve</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No patients in queue.</td></tr>
        <?php endif; ?>
    </table>

    <h3>Add Patient to Queue</h3>
    <form method="POST">
        <input type="text" name="patient_name" placeholder="Patient Name" required>
        <select name="doctor_id">
            <option value="">Select Doctor (optional)</option>
            <?php while($doc = $doctors->fetch_assoc()): ?>
                <option value="<?= $doc['id'] ?>"><?= htmlspecialchars($doc['username']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="add_queue"><i class="fa-solid fa-plus"></i> Add to Queue</button>
    </form>
</div>

</body>
</html>
