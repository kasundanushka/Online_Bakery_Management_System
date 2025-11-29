<?php
session_start();
include('db.php');

// Security Check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: Home.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']); // Simple URL input for image

    if (empty($name) || empty($price) || empty($category)) {
        $error = "Name, Price, and Category are required fields.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a positive number.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bakeryitems (Name, Price, category, description, Image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $name, $price, $category, $description, $image_url);
        
        if ($stmt->execute()) {
            $message = "New item **{$name}** added successfully!";
            // Optionally, clear POST data to prevent double submission
            $_POST = array(); 
        } else {
            $error = "Error adding item: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add New Item</title>
    <style>
        /* Reuse/adapt styles */
        body { font-family: Arial, sans-serif; background-color: #f5f0e8; padding: 20px; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #267026; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea {
            width: 100%; padding: 10px; margin-bottom: 15px; 
            border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        textarea { resize: vertical; }
        .save-btn { background-color: #267026; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .save-btn:hover { background-color: #1e5a1e; }
        .back-link { margin-top: 20px; display: block; }
        .message-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .message-error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add New Bakery Item</h1>

        <?php if ($message): ?>
            <div class="message-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Item Name:</label>
            <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

            <label for="category">Category:</label>
            <input type="text" name="category" id="category" required value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>">
            
            <label for="price">Price (LKR):</label>
            <input type="number" name="price" id="price" step="0.01" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">

            <label for="image_url">Image URL:</label>
            <input type="text" name="image_url" id="image_url" placeholder="e.g., images/croissant.jpg" value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>">

            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            
            <button type="submit" class="save-btn">Add Item</button>
        </form>

        <a href="admin_panel.php" class="back-link">← Back to Item List</a>
    </div>
</body>
</html>