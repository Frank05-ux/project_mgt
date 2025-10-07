<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");

$conn = new mysqli("localhost","root","","cars_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $model = $_POST['model'];
    $price = $_POST['price'];

    $image_path = '';
    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $upload_dir = 'uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time().'_'.basename($_FILES['image']['name']);
        $target_file = $upload_dir.$file_name;
        if(move_uploaded_file($_FILES['image']['tmp_name'],$target_file)) $image_path = $target_file;
    }

    $stmt = $conn->prepare("INSERT INTO car (name, model, price, image) VALUES (?,?,?,?)");
    $stmt->bind_param("ssds",$name,$model,$price,$image_path);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: inventory.php?added=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Car</title>
<style>
body{ font-family:sans-serif; background:#1e1e2f; color:#fff; text-align:center; }
form{ background:#2e3440; padding:30px; border-radius:12px; margin:50px auto; max-width:500px; color:#fff; }
input,button{ width:100%; padding:12px; margin:10px 0; border-radius:8px; border:none; }
button{ background:#00bfff; cursor:pointer; }
</style>
</head>
<body>
<h2>Add New Car</h2>
<form method="POST" enctype="multipart/form-data">
<input type="text" name="name" placeholder="Car Name" required>
<input type="text" name="model" placeholder="Model">
<input type="number" name="price" step="0.01" placeholder="Price" required>
<input type="file" name="image">
<button type="submit" name="submit">Add Car</button>
</form>
<a href="inventory.php" style="color:#00bfff;">Back to Inventory</a>
</body>
</html>
