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

// --- Handle Message Deletion ---
if (isset($_POST['action']) && $_POST['action'] == 'delete_message' && isset($_POST['message_id'])) {
    $message_id_to_delete = (int)$_POST['message_id'];
    
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id = ?");
    $stmt->bind_param("i", $message_id_to_delete);
    
    if ($stmt->execute()) {
        $message = "Message ID **{$message_id_to_delete}** deleted successfully.";
    } else {
        $error = "Error deleting message: " . $stmt->error;
    }
    $stmt->close();
}


// --- Fetch all messages with customer names ---
$messages = [];
// Fetch all messages from the contact_messages table, ordered by submission time (most recent first)
$query = "SELECT message_id, name, email, phone_number, subject, message, submitted_at FROM contact_messages ORDER BY submitted_at DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Customer Messages</title>
    <style>
        /* Reuse/adapt styles from admin_panel.php */
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
            color: #333; padding: 10px 20px; 
            border: none; cursor: pointer; 
            margin-right: 10px; 
            border-radius: 4px; 
            transition: background-color 0.3s; 
        }
        .tab-menu button:hover, 
        .tab-menu button.active { 
            background-color: #267026; 
            color: white; 
        }
        .tab-menu button.active { 
            background-color: #267026; 
            color: white; }
        
        .content-section { 
            margin-top: 20px; 
        }
        .message-card { 
            background: #f9f9f9; 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 6px; 
            position: relative; 
        }
        .message-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px dashed #ccc; 
            padding-bottom: 5px; 
            margin-bottom: 10px; 
        }
        .subject-title { 
            font-weight: bold; 
            color: #267026; 
            font-size: 1.1em; 
        }
        .message-info { 
            font-size: 0.9em; 
            color: #666; 
            
        }
        .message-body { 
            margin-top: 10px; 
            white-space: pre-wrap; 
        }

        /* Delete Button Styles */
        .delete-form { 
            position: absolute; 
            top: 15px; right: 15px; 
        }
        .delete-btn { 
            background-color: #dc3545; 
            color: white; 
            padding: 5px 10px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-weight: bold;
        }
        .delete-btn:hover { 
            background-color: #c82333; 
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
        <h1>Admin Panel - Customer Messages</h1>
        
        <div class="tab-menu">
            <button onclick="location.href='admin_panel.php'">Item Management</button>
            <button onclick="location.href='admin_users.php'">User Management</button>
            <button onclick="location.href='admin_orders.php'">Order Management</button>
            <button class="active">Customer Messages</button>
        </div>
        
        <div class="content-section">
            <h2>All Contact Inquiries (<?php echo count($messages); ?>)</h2>

            <?php if ($message): ?>
                <div class="message-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-card">
                        
                        <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to permanently delete this message?');">
                            <input type="hidden" name="action" value="delete_message">
                            <input type="hidden" name="message_id" value="<?php echo $msg['message_id']; ?>">
                            <br><br><button type="submit" class="delete-btn">Remove</button>
                        </form>

                        <div class="message-header">
                            <span class="subject-title">Subject: <?php echo htmlspecialchars($msg['subject']); ?></span>
                            <span class="message-info">Received: <?php echo date('Y-m-d H:i', strtotime($msg['submitted_at'])); ?></span>
                        </div>
                        <div class="message-info">
                            From: **<?php echo htmlspecialchars($msg['name']); ?>** (<?php echo htmlspecialchars($msg['email']); ?>)
                            <?php if (!empty($msg['phone_number'])): ?>
                                | Phone: <?php echo htmlspecialchars($msg['phone_number']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="message-body">
                            <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; border: 1px dashed #ccc; border-radius: 5px; color: #666;">
                    <p>No customer contact messages found.</p>
                </div>
            <?php endif; ?>

            <p style="margin-top: 20px;font-size:19px;"><a href="Home.php">← Back to Home</a></p>
        </div>
    </div>
</body>
</html>