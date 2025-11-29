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

// --- Handle User Deletion ---
if (isset($_POST['action']) && $_POST['action'] == 'delete_user' && isset($_POST['user_id'])) {
    $user_id_to_delete = (int)$_POST['user_id'];
    
    // Prevent admin from deleting their own account
    if ($user_id_to_delete == $_SESSION['user_id']) {
        $error = "Error: You cannot delete your own active admin account.";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id_to_delete);
        
        if ($stmt->execute()) {
            $message = "User ID **{$user_id_to_delete}** deleted successfully.";
        } else {
            $error = "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- Handle Admin Status Change (Promote/Demote) ---
if (isset($_POST['action']) && $_POST['action'] == 'toggle_admin' && isset($_POST['user_id']) && isset($_POST['new_status'])) {
    $user_id_to_toggle = (int)$_POST['user_id'];
    $new_status = (int)$_POST['new_status']; // 1 for promote, 0 for demote

    // Prevent admin from demoting their own account while logged in
    if ($user_id_to_toggle == $_SESSION['user_id'] && $new_status == 0) {
        $error = "Error: You cannot demote your own active admin account.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $new_status, $user_id_to_toggle);

        if ($stmt->execute()) {
            $action_text = ($new_status == 1) ? 'Promoted to Admin' : 'Demoted to Customer';
            $message = "User ID **{$user_id_to_toggle}** successfully **{$action_text}**.";
        } else {
            $error = "Error changing admin status: " . $stmt->error;
        }
        $stmt->close();
    }
}


// --- Fetch all users ---
$users = [];
$query = "SELECT user_id, full_name, email, phone, is_admin, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management</title>
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
        .action-btn { 
            padding: 5px 10px; 
            margin-right: 5px; 
            cursor: pointer; 
            border-radius: 4px; 
            border: none; 
        }
        .delete-btn { 
            background-color: #dc3545; 
            color: white; 
        }
        .promote-btn { 
            background-color: #007bff; 
            color: white; 
        }
        .demote-btn { 
            background-color: #ffc107; 
            color: #333; 
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
        <h1>Admin Panel - User Management</h1>
        
        <div class="tab-menu">
            <button onclick="location.href='admin_panel.php'">Item Management</button>
            <button class="active">User Management</button>
            <button onclick="location.href='admin_orders.php'">Order Management</button>
            <button onclick="location.href='admin_messages.php'">Customer Messages</button>
        </div>
        
        <div class="content-section">
            <h2>All Registered Users (<?php echo count($users); ?>)</h2>

            <?php if ($message): ?>
                <div class="message-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php 
                                        $status = $user['is_admin'] ? '<strong style="color: #267026;">Admin</strong>' : 'Customer';
                                        echo $status;
                                    ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_admin">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <?php if ($user['is_admin']): ?>
                                            <input type="hidden" name="new_status" value="0">
                                            <button type="submit" class="action-btn demote-btn" onclick="return confirm('Are you sure you want to DEMOTE <?php echo htmlspecialchars($user['full_name']); ?>?');">Demote</button>
                                        <?php else: ?>
                                            <input type="hidden" name="new_status" value="1">
                                            <button type="submit" class="action-btn promote-btn">Promote</button>
                                        <?php endif; ?>
                                    </form>

                                    <form method="POST" style="display:inline;" onsubmit="return confirm('WARNING: Deleting user <?php echo htmlspecialchars($user['full_name']); ?> will also delete all their orders and cart items. Continue?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No users registered yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <p style="margin-top: 20px; font-size:19px;"><a href="Home.php">← Back to Home</a></p>
        </div>
    </div>
</body>
</html>