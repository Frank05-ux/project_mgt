<?php
session_start();
// ✅ Check if patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}
// ✅ Database connection
$conn = new mysqli('localhost', 'root', '', 'hospital_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
// ✅ Fetch patient info
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id='$user_id' AND role='patient'")->fetch_assoc();
// ✅ Fetch recent appointments
$appointments = $conn->query("SELECT * FROM appointments WHERE patient_id='$user_id' ORDER BY appointment_date DESC, appointment_time DESC LIMIT 5");
// ✅ Fetch recent notifications
$notifications = $conn->query("SELECT * FROM notifications WHERE recipient_role='patient' ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* {
    box-sizing: border-box;
}
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #2575fc, #6a11cb);
    margin: 0;
    padding: 0;
    color: #333;
    overflow-x: hidden;
    animation: fadeIn 0.8s ease-in-out;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(10px);}
    to {opacity: 1; transform: translateY(0);}
}
header {
    background: rgba(0,0,0,0.8);
    color: #fff;
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}
header h2 {
    letter-spacing: 1px;
    font-size: 22px;
}
header a {
    color: #fff;
    background: #00d4ff;
    padding: 10px 18px;
    border-radius: 30px;
    text-decoration: none;
    transition: 0.3s;
    font-weight: 600;
}
header a:hover {
    background: #fff;
    color: #2575fc;
    transform: scale(1.05);
}
.container {
    background: rgba(255,255,255,0.97);
    margin: 40px auto;
    padding: 40px;
    border-radius: 20px;
    width: 85%;
    max-width: 1200px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    transition: 0.4s ease;
}
.container:hover {
    box-shadow: 0 18px 40px rgba(0,0,0,0.35);
}
h3 {
    color: #2575fc;
    margin-bottom: 20px;
    font-weight: 700;
}
form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 30px;
    align-items: flex-end;
}
form input[type="text"], 
form input[type="email"], 
form input[type="password"] {
    padding: 12px 14px;
    border-radius: 8px;
    border: 1px solid #ccc;
    flex: 1;
    font-size: 15px;
    transition: all 0.3s;
}
form input:focus {
    border-color: #2575fc;
    box-shadow: 0 0 6px rgba(37,117,252,0.3);
    outline: none;
}
form button {
    padding: 12px 22px;
    border: none;
    background: linear-gradient(135deg, #2575fc, #6a11cb);
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
    box-shadow: 0 3px 10px rgba(37,117,252,0.3);
}
form button:hover {
    background: linear-gradient(135deg, #1a5dd1, #4b0fb6);
    transform: scale(1.05);
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    animation: fadeIn 0.6s ease;
}
th, td {
    padding: 14px;
    border-bottom: 1px solid #eee;
    text-align: left;
    word-break: break-word;
}
th {
    background: #2575fc;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
tr:nth-child(even) {
    background: #f9f9ff;
}
tr:hover {
    background: #e9f1ff;
    transition: 0.2s;
}
.success, .error {
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-weight: 600;
    animation: fadeIn 0.4s ease-in;
}
.success {
    color: #155724;
    background: #d4edda;
    border: 1px solid #c3e6cb;
}
.error {
    color: #721c24;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
}

/* ✅ Responsive Design */
@media (max-width: 900px) {
    header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    .container {
        padding: 25px;
        width: 90%;
    }
    form {
        flex-direction: column;
        align-items: stretch;
    }
    form input, form button {
        width: 100%;
    }
    table, thead, tbody, th, td, tr {
        display: block;
    }
    thead tr {
        display: none;
    }
    tr {
        margin-bottom: 15px;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 10px;
        background: #fff;
    }
    td {
        padding: 10px 12px;
        text-align: right;
        position: relative;
    }
    td::before {
        content: attr(data-label);
        position: absolute;
        left: 12px;
        font-weight: 600;
        text-transform: capitalize;
        color: #2575fc;
    }
}
</style>
</head>
<body>

<header>
    <h2><i class="fa-solid fa-user"></i> Patient Dashboard</h2>
    <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
</header>

<div class="container">
    <h3>👋 Welcome, <?= htmlspecialchars($user['username'] ?? $user['email']) ?></h3>
    <p style="margin-bottom:28px; color:#2575fc; font-size:1.08rem;">This is your personal dashboard. Here you can view your profile, recent appointments, and notifications.</p>

    <div style="background:#f7fbff; border-radius:14px; padding:18px 22px; margin-bottom:28px; box-shadow:0 2px 8px #b3d1ff33;">
        <h4 style="color:#2575fc; margin-bottom:10px;">Your Profile</h4>
        <div style="font-size:1.05rem; color:#333;">
            <strong>Name:</strong> <?= htmlspecialchars($user['username'] ?? '-') ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '-') ?><br>
            <strong>Role:</strong> <?= ucfirst($user['role']) ?><br>
        </div>
    </div>

    <h3><i class="fa-solid fa-calendar-check"></i> Recent Appointments</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Time</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php if ($appointments->num_rows > 0): ?>
            <?php while($a = $appointments->fetch_assoc()): ?>
            <tr>
                <td data-label="Date"><?= htmlspecialchars($a['appointment_date']) ?></td>
                <td data-label="Time"><?= htmlspecialchars($a['appointment_time']) ?></td>
                <td data-label="Status"><?= ucfirst($a['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center;">No appointments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <h3><i class="fa-solid fa-bell"></i> Notifications</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Message</th></tr>
        </thead>
        <tbody>
        <?php if ($notifications->num_rows > 0): ?>
            <?php while($n = $notifications->fetch_assoc()): ?>
            <tr>
                <td data-label="Date"><?= htmlspecialchars($n['created_at']) ?></td>
                <td data-label="Message"><?= htmlspecialchars($n['message']) ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2" style="text-align:center;">No notifications found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:32px; text-align:center;">
        <a href="queue.php" style="color:#2575fc; text-decoration:underline; font-size:1.05rem; margin-right:18px;">View Queue</a>
        <a href="index.php" style="color:#2575fc; text-decoration:underline; font-size:1.05rem;">Home</a>
    </div>
</div>

</body>
</html>
