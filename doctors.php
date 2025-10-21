<?php
session_start();

// ✅ Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// ✅ Database connection
$conn = new mysqli('localhost', 'root', '', 'hospital_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ✅ Handle Add Doctor form submission
if (isset($_POST['add_doctor'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'doctor')");
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
        $success = "Doctor added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ Fetch all doctors
$result = $conn->query("SELECT * FROM users WHERE role='doctor' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Doctors</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    font-family: 'Inter', sans-serif;
    background: url('https://images.unsplash.com/photo-1580281657521-43e90a6f06b6?auto=format&fit=crop&w=1400&q=80') no-repeat center center/cover;
    margin: 0; padding: 0;
    color: #333; backdrop-filter: blur(6px);
}
header {
    background: rgba(37,117,252,0.9);
    color: white;
    padding: 20px 40px;
    display: flex; justify-content: space-between; align-items: center;
}
header a {
    color: white; background: rgba(255,255,255,0.2);
    padding: 10px 18px; border-radius: 6px; text-decoration: none;
    transition: 0.3s;
}
header a:hover { background: rgba(255,255,255,0.3); }
.container {
    background: rgba(255,255,255,0.95);
    margin: 40px auto; padding: 30px;
    border-radius: 12px; width: 85%;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
h3 { margin-bottom: 20px; }
form {
    display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 30px; align-items: flex-end;
}
form input[type="text"], form input[type="email"], form input[type="password"] {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    flex: 1;
}
form button {
    padding: 10px 20px;
    border: none;
    background: #2575fc;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}
form button:hover { background: #1a5dd1; }
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
}
th, td { padding: 12px; border-bottom: 1px solid #eee; }
th { background: #2575fc; color: white; }
tr:hover { background: #f8f8f8; }
.success { color: green; margin-bottom: 15px; }
.error { color: red; margin-bottom: 15px; }
</style>
</head>
<body>

<header>
    <h2><i class="fa-solid fa-user-md"></i> Manage Doctors</h2>
    <a href="admin_dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
</header>

<div class="container">

    <h3>Add New Doctor</h3>

    <?php if(!empty($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="add_doctor"><i class="fa-solid fa-plus"></i> Add Doctor</button>
    </form>

    <h3>All Registered Doctors</h3>
    <table>
        <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= ucfirst($row['role']) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if($result->num_rows == 0): ?>
        <tr><td colspan="4" style="text-align:center;">No doctors found.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
