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

// --- Handle Item Deletion ---
if (isset($_POST['action']) && $_POST['action'] == 'delete_item' && isset($_POST['item_id'])) {
    $item_id_to_delete = (int)$_POST['item_id'];
    
    $stmt = $conn->prepare("DELETE FROM bakeryitems WHERE item_id = ?");
    $stmt->bind_param("i", $item_id_to_delete);
    
    if ($stmt->execute()) {
        $message = "Item ID **{$item_id_to_delete}** deleted successfully.";
    } else {
        $error = "Error deleting item: " . $stmt->error;
    }
    $stmt->close();
}


// --- Fetch all items for display ---
$items = [];
$result = $conn->query("SELECT item_id, Name, Price, category FROM bakeryitems ORDER BY item_id ASC");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Item Management</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f5f0e8; padding: 20px; 
        }
        .admin-container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        h1 { 
            color: #267026; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #eee; 
            padding-bottom: 10px; 
        }
        .tab-menu button { 
            background-color: #f2f2f2; 
            color: #333; 
            padding: 10px 20px; 
            border: none; 
            cursor: pointer; 
            margin-right: 10px; 
            border-radius: 4px; 
            transition: background-color 0.3s; 
        }
        .tab-menu button:hover, 
        .tab-menu button.active { 
            background-color: #267026; 
            color: white; 
        }
        .content-section { 
            margin-top: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .action-btn { 
            padding: 5px 10px; 
            margin-right: 5px; 
            cursor: pointer; 
            border-radius: 4px; 
            border: none; 
            font-weight: bold; 
        }
        .edit-btn { 
            background-color: #ffc107; 
            color: #333; 
        }
        .delete-btn { 
            background-color: #dc3545; 
            color: white; 
        }
        .add-btn { 
            background-color: #267026; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            margin-bottom: 15px; 
            float: right;
            cursor: pointer; 
            border-radius: 4px; 
            font-weight: bold;
        }
        .message-success { 
            background: #d4edda; 
            color: #155724; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 15px; 
        }
        .message-error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 15px; 
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Panel - Item Management</h1>
        
        <div class="tab-menu">
            <button class="active" onclick="location.href='admin_panel.php'">Item Management</button>
            <button onclick="location.href='admin_users.php'">User Management</button>
            <button onclick="location.href='admin_orders.php'">Order Management</button>
            <button onclick="location.href='admin_messages.php'">Customer Messages</button>
        </div>
        
        <div class="content-section">
            <h2>Bakery Items List</h2>

            <?php if ($message): ?>
                <div class="message-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <button class="add-btn" onclick="location.href='add_item.php'">+ Add New Item</button>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th style="width: 120px;">Price (LKR)</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($items) > 0): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['item_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['Name']); ?></td>
                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($item['Price'], 2)); ?></td>
                                <td>
                                    <button class="action-btn edit-btn" onclick="location.href='edit_item.php?id=<?php echo $item['item_id']; ?>'">Edit</button>
                                    
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($item['Name']); ?>? This action is irreversible.');">
                                        <input type="hidden" name="action" value="delete_item">
                                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No items found in the database.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <p style="margin-top: 20px; font-size:19px;"><a href="Home.php">← Back to Home</a></p>
        </div>
    </div>
</body>
</html>