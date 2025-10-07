<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: admin_login.php");

$conn = new mysqli("localhost","root","","admin_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM car WHERE id=$id");
$car = $result->fetch_assoc();
if(!$car) { echo "Car not found"; exit(); }

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $image_path = $car['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $upload_dir = 'uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time().'_'.basename($_FILES['image']['name']);
        $target_file = $upload_dir.$file_name;
        if(move_uploaded_file($_FILES['image']['tmp_name'],$target_file)) $image_path = $target_file;
    }

    $stmt = $conn->prepare("UPDATE car SET name=?, model=?, price=?, status=?, image=? WHERE id=?");
    $stmt->bind_param("ssdssi",$name,$model,$price,$status,$image_path,$id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: inventory.php?updated=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Car</title>
<style>
body{ font-family:sans-serif; background:#1e1e2f; color:#fff; text-align:center; }
form{ background:#2e3440; padding:30px; border-radius:12px; margin:50px auto; max-width:500px; color:#fff; }
input,select,button{ width:100%; padding:12px; margin:10px 0; border-radius:8px; border:none; }
button{ background:#00bfff; cursor:pointer; }
img{ max-width:150px; margin:10px 0; border-radius:8px; }
</style>
</head>
<body>
<h2>Edit Car</h2>
<form method="POST" enctype="multipart/form-data">
<input type="text" name="name" value="<?= htmlspecialchars($car['name']) ?>" required>
<input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>">
<input type="number" name="price" step="0.01" value="<?= $car['price'] ?>" required>
<select name="status">
<option value="Available" <?= $car['status']=='Available'?'selected':'' ?>>Available</option>
<option value="Sold" <?= $car['status']=='Sold'?'selected':'' ?>>Sold</option>
</select>
<?php if($car['image']) echo "<img src='".$car['image']."' alt='Car'>"; ?>
<input type="file" name="image">
<button type="submit" name="submit">Update Car</button>
</form>
<a href="inventory.php" style="color:#00bfff;">Back to Inventory</a>
</body>
</html>
