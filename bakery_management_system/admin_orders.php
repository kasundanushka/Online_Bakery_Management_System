<?php
session_start();
include('db.php');

// Security Check: Only logged-in Admins can access this page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: Home.php");
    exit();
}

$message = '';
$error = '';

// --- Handle Order Status Update ---
if (isset($_POST['action']) && $_POST['action'] == 'update_status' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];

    $valid_statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        $error = "Invalid status selected.";
    } else {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            $message = "Order ID **{$order_id}** status updated to **{$new_status}**.";
        } else {
            $error = "Error updating order status: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- Fetch all orders with customer names ---
$orders = [];
$query = "
    SELECT 
        o.order_id, u.full_name, o.total_amount, o.order_status, o.order_date, o.delivery_address 
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management</title>
    <style>
        /* Base Admin Styles (adapt from your Home.php/admin_panel.php styles) */
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f5f0e8; 
            padding: 20px; 
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
        .status-select { 
            padding: 5px; 
            border-radius: 4px; 
            border: 1px solid #ccc; 
        }
        .update-btn { 
            background-color: #28a745; 
            color: white; 
            padding: 5px 10px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin-left: 5px; 
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
        /* Color coding for status */
        .status-Pending { 
            color: orange; 
            font-weight: bold; 
        }
        .status-Processing { 
            color: blue; 
            font-weight: bold; 
        }
        .status-Completed { 
            color: green; 
            font-weight: bold; 
        }
        .status-Cancelled { 
            color: red; 
            font-weight: bold; 
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Panel - Order Management</h1>
        
        <div class="tab-menu">
            <button onclick="location.href='admin_panel.php'">Item Management</button>
            <button onclick="location.href='admin_users.php'">User Management</button>
            <button class="active">Order Management</button>
            <button onclick="location.href='admin_messages.php'">Customer Messages</button>
        </div>
        
        <div class="content-section">
            <h2>All Customer Orders (<?php echo count($orders); ?>)</h2>

            <?php if ($message): ?>
                <div class="message-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Total Amount (LKR)</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                        </tr>
                </thead>
                <tbody>
                    <?php $statuses = ['Pending', 'Processing', 'Completed', 'Cancelled']; ?>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                <td class="status-<?php echo htmlspecialchars($order['order_status']); ?>"><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        
                                        <select name="status" class="status-select">
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?php echo $status; ?>" <?php echo ($order['order_status'] == $status) ? 'selected' : ''; ?>>
                                                    <?php echo $status; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="update-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <p style="margin-top: 20px; font-size:19px;"><a href="Home.php">← Back to Home</a></p>
        </div>
    </div>
</body>
</html>