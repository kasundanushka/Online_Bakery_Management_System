<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: sign_in.html");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: cart.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data
    $user_id = $_SESSION['user_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $delivery_address = trim($_POST['delivery_address']);
    $payment_method = $_POST['payment_method'];
    $order_notes = isset($_POST['order_notes']) ? trim($_POST['order_notes']) : '';
    
    // Calculate total
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert order into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_status, payment_method, delivery_address) VALUES (?, ?, 'Pending', ?, ?)");
        $stmt->bind_param("idss", $user_id, $total_amount, $payment_method, $delivery_address);
        $stmt->execute();
        
        // Get the order ID
        $order_id = $conn->insert_id;
        
        // Insert each cart item into order_items table
        $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($_SESSION['cart'] as $item) {
            // Get item_id from bakeryitems table
            $item_stmt = $conn->prepare("SELECT item_id FROM bakeryitems WHERE Name = ?");
            $item_stmt->bind_param("s", $item['name']);
            $item_stmt->execute();
            $item_result = $item_stmt->get_result();
            
            if ($item_result->num_rows > 0) {
                $item_data = $item_result->fetch_assoc();
                $item_id = $item_data['item_id'];
                
                // Insert order item
                $stmt2->bind_param("iiid", $order_id, $item_id, $item['quantity'], $item['price']);
                $stmt2->execute();
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Clear the cart
        unset($_SESSION['cart']);
        
        // Store order ID for confirmation page
        $_SESSION['order_id'] = $order_id;
        $_SESSION['order_total'] = $total_amount;
        
        // Redirect to order confirmation page
        header("Location: order_confirmation.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        $_SESSION['order_error'] = "Order failed. Please try again.";
        header("Location: checkout.php");
        exit();
    }
    
    $stmt->close();
    $stmt2->close();
    $conn->close();
    
} else {
    header("Location: checkout.php");
    exit();
}
?>