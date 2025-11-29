<?php
session_start();
include('db.php');

// Security Check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: Home.php");
    exit();
}

$item = null;
$message = '';
$error = '';
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($item_id <= 0) {
    $error = "Invalid Item ID.";
} else {
    // --- Handle Form Submission (Update) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name']);
        $price = (float)$_POST['price'];
        $category = trim($_POST['category']);
        $description = trim($_POST['description']);
        $image_url = trim($_POST['image_url']); 

        if (empty($name) || empty($price) || empty($category)) {
            $error = "Name, Price, and Category are required fields.";
        } elseif (!is_numeric($price) || $price <= 0) {
            $error = "Price must be a positive number.";
        } else {
            $stmt = $conn->prepare("UPDATE bakeryitems SET Name = ?, Price = ?, category = ?, description = ?, Image = ? WHERE item_id = ?");
            $stmt->bind_param("sdsssi", $name, $price, $category, $description, $image_url, $item_id);
            
            if ($stmt->execute()) {
                $message = "Item **{$name}** updated successfully!";
            } else {
                $error = "Error updating item: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // --- Fetch Item Data (for initial load and after update) ---
    $stmt = $conn->prepare("SELECT item_id, Name, Price, category, description, Image FROM bakeryitems WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $item = $result->fetch_assoc();
    } else {
        $error = "Item not found.";
    }
    $stmt->close();
}

$conn->close();

// If the item was not found, stop execution
if (!$item && !$error) {
    $error = "Could not load item details.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Item</title>
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
        <h1>Edit Bakery Item (ID: <?php echo $item_id; ?>)</h1>

        <?php if ($message): ?>
            <div class="message-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($item): ?>
            <form method="POST">
                <label for="name">Item Name:</label>
                <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($item['Name']); ?>">

                <label for="category">Category:</label>
                <input type="text" name="category" id="category" required value="<?php echo htmlspecialchars($item['category']); ?>">
                
                <label for="price">Price (LKR):</label>
                <input type="number" name="price" id="price" step="0.01" required value="<?php echo htmlspecialchars($item['Price']); ?>">

                <label for="image_url">Image URL:</label>
                <input type="text" name="image_url" id="image_url" placeholder="e.g., images/croissant.jpg" value="<?php echo htmlspecialchars($item['Image']); ?>">

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>
                
                <button type="submit" class="save-btn">Save Changes</button>
            </form>
        <?php endif; ?>

        <a href="admin_panel.php" class="back-link">← Back to Item List</a>
    </div>
</body>
</html>