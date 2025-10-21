
<?php
// index.php - Hospital Management System Landing Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255,255,255,0.92);
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(112,183,255,0.13);
            padding: 48px 36px 36px 36px;
            text-align: center;
            max-width: 420px;
            width: 100%;
        }
        h1 {
            font-size: 2.2rem;
            color: #0546a0;
            margin-bottom: 12px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        p {
            color: #333;
            font-size: 1.08rem;
            margin-bottom: 28px;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 18px;
        }
        a.button {
            display: block;
            padding: 13px;
            background: linear-gradient(90deg, #0546a0 0%, #70b7ff 100%);
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            font-size: 1.08rem;
            box-shadow: 0 2px 8px rgba(112,183,255,0.10);
            transition: background 0.3s, transform 0.3s;
        }
        a.button:hover {
            background: linear-gradient(90deg, #70b7ff 0%, #0546a0 100%);
            transform: scale(1.04);
        }
        .logo {
            width: 80px;
            margin-bottom: 18px;
            filter: drop-shadow(0 2px 8px #b3d1ff88);
        }
        footer {
            margin-top: 32px;
            color: #0546a0;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="hospital image/hospital.png" alt="Hospital Logo" class="logo">
        <h1>Patient Queue Management System</h1>
        <p>Welcome. Please select your role to login or register.</p>
        <div class="btn-group">
            <a href="admin_login.php" class="button">Admin Login</a>
            <a href="register.php" class="button">Patient Registration</a>
            <a href="login.php" class="button">Patient Login</a>
            <a href="receptionist_login.php" class="button">Receptionist Login</a>
        </div>
        <footer>© <?php echo date('Y'); ?> Patient Queue Management System</footer>
    </div>
</body>
</html>
