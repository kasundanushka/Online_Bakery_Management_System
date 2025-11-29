<?php
// Start the session to track logged-in users
session_start();

// Include database connection file
include('db.php');

// Check if user is logged in, if not redirect to sign in page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: sign_in.html");
    exit();
}

// Get the logged-in user's ID from session
$user_id = $_SESSION['user_id'];

// Get all orders for this user from database
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Sweet Bakery</title>
    <link rel="stylesheet" href="css.css">
    <style>
        

        /* Orders page styles */
        .orders-section {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title h1 {
            color: rgb(38, 112, 38);
            font-size: 40px;
            margin-bottom: 10px;
        }

        .page-title p {
            color: rgb(101, 122, 106);
            font-size: 18px;
        }

        /* Each order card */
        .order-box {
            background: white;
            border: 2px solid #e8f5e9;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .order-box:hover {
            box-shadow: 0 4px 15px rgba(38, 112, 38, 0.15);
        }

        /* Top part of order card */
        .order-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e8f5e9;
        }

        .order-number {
            font-size: 22px;
            font-weight: bold;
            color: rgb(38, 112, 38);
        }

        /* Status badge */
        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Order information grid */
        .order-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 13px;
            color: rgb(101, 122, 106);
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        /* View items button */
        .view-btn {
            background-color: rgb(40, 100, 40);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
        }

        .view-btn:hover {
            background-color: rgb(30, 80, 30);
        }

        /* Hidden items section */
        .order-items-list {
            display: none;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e8f5e9;
        }

        .order-items-list.show {
            display: block;
        }

        .order-items-list h3 {
            color: rgb(38, 112, 38);
            margin-bottom: 15px;
            font-size: 20px;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: #fafafa;
            border-radius: 8px;
            overflow: hidden;
        }

        .items-table th,
        .items-table td {
            padding: 15px;
            text-align: left;
        }

        .items-table th {
            background-color: rgb(38, 112, 38);
            color: white;
            font-weight: 600;
        }

        .items-table tr:nth-child(even) {
            background-color: white;
        }

        .items-table tr:hover {
            background-color: #e8f5e9;
        }

        /* Empty state when no orders */
        .no-orders-box {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 12px;
            border: 2px dashed #e8f5e9;
        }

        .no-orders-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .no-orders-box h2 {
            color: rgb(38, 112, 38);
            font-size: 28px;
            margin-bottom: 15px;
        }

        .no-orders-box p {
            color: rgb(101, 122, 106);
            font-size: 16px;
            margin-bottom: 30px;
        }

        .shop-btn {
            display: inline-block;
            padding: 15px 40px;
            background-color: rgb(40, 100, 40);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
        }

        .shop-btn:hover {
            background-color: rgb(30, 80, 30);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f5f0e8;
            padding: 30px 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Logo Section */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 40px;
            color: rgb(38, 112, 38);
            font-weight: bold;
        }

        .logo-circle {
            width: 45px;
            height: 45px;
            background-color: rgb(38, 112, 38);
            color: white;
            border-radius: 75%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
        }

        /* Center Navigation Links */
        .nav-links {
            list-style: none;
            display: flex;
            gap: 35px;
            margin: 0;
            padding: 0;
            flex: 1;
            justify-content: right;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-size: 20px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: rgb(38, 112, 38);
            border-bottom: 2px solid #333;
        }

        /* Right Section - Cart & Sign In */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Sign In Button */
        .sign-in-btn {
            background-color: rgb(38, 112, 38);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.3s;
        }

        .sign-in-btn:hover {
            background-color: rgb(30, 90, 30);
        }

        /* User Menu */
        .user-menu {
            position: relative;
        }

        .user-btn {
            background-color: transparent;
            border: 2px solid rgb(38, 112, 38);
            color: rgb(38, 112, 38);
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .user-btn:hover {
            background-color: rgb(38, 112, 38);
            color: white;
        }

        .user-icon {
            width: 28px;
            height: 28px;
            background: rgb(38, 112, 38);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 45px;
            background-color: white;
            min-width: 200px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            border-radius: 8px;           
            overflow: hidden;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #f5f5f5;
        }

        .dropdown-content a:last-child {
            color: #dc3545;
            border-top: 1px solid #eee;
        }

        .user-menu:hover .dropdown-content {
            display: block;
        }

        /* Mobile Menu Icon */
        .menu-icon {
            display: none;
            font-size: 28px;
            cursor: pointer;
            color: rgb(38, 112, 38);
        }

        /* Mobile Responsive */
        @media (max-width:832px) {
            .navbar {
                padding: 15px 20px;
            }

            .menu-icon {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 75px;
                left: 0;
                right: 0;
                background-color: #f5f0e8;
                flex-direction: column;
                gap: 0;
                padding: 20px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 999;
            }

            .nav-links.show {
                display: flex;
            }

            .nav-links li {
                padding: 12px 0;
                border-bottom: 1px solid #e0d5c5;
            }

            .nav-links li:last-child {
                border-bottom: none;
            }

            .nav-right {
                gap: 15px;
            }

            .user-btn {
                font-size: 14px;
                padding: 6px 15px;
            }

            .sign-in-btn {
                font-size: 14px;
                padding: 8px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
    <nav class="navbar">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-circle">S</div>
            <span>Sweet Bakery</span>
        </div>

        <!-- Mobile Menu Icon -->
        <div class="menu-icon" onclick="toggleMenu()">☰</div>
        
        <!-- Navigation Links -->
        <ul class="nav-links" id="navLinks">
            <li><a href="Home.php" >Home</a></li>
            <li><a href="Menu.php">Menu</a></li>
            <li><a href="About.php">About</a></li>
            <li><a href="Contact.php">Contact</a></li>
            <li><a href="cart.php" class="active">Cart</a></li>
        

        <!-- Right Section: Sign In -->
        <div class="nav-right">
            <!-- User Menu or Sign In Button -->
            <?php
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                $first_letter = strtoupper(substr($_SESSION['user_name'], 0, 1));
                
                // New Code in Home.php
                echo '<div class="user-menu">';
                echo '<button class="user-btn">';
                echo '<span class="user-icon">' . $first_letter . '</span>';
                echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]);
                echo '</button>';
                echo '<div class="dropdown-content">';
                echo '<a href="profile.php">My Profile</a>';
                echo '<a href="my_orders.php">My Orders</a>';

                // **CRITICAL UPDATE: Add Admin Panel link**
                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
                    echo '<a href="admin_panel.php" style="color:rgb(38, 112, 38);">Admin Panel</a>';
                }

                echo '<a href="logout.php" style="color:#dc3545;">Logout</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<a href="sign_in.php"><button class="sign-in-btn">Sign In</button></a>';
            }
            ?>
        </div>
        </ul>
    </nav><br><br>
    
        <!-- Orders Section -->
        <div class="orders-section">
            <div class="page-title">
                <h1>My Orders</h1>
                <p>Track your orders and view order history</p>
            </div>

            <?php if (empty($orders)): ?>
                <!-- Show this if user has no orders -->
                <div class="no-orders-box">
                    <div class="no-orders-icon">📦</div>
                    <h2>No Orders Yet</h2>
                    <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="Menu.php" class="shop-btn">Shop Now</a>
                </div>
            <?php else: ?>
                <!-- Show all orders -->
                <?php foreach ($orders as $order): ?>
                    <div class="order-box">
                        <!-- Order header -->
                        <div class="order-top">
                            <div class="order-number">Order #<?php echo $order['order_id']; ?></div>
                            <span class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                                <?php echo $order['order_status']; ?>
                            </span>
                        </div>

                        <!-- Order details -->
                        <div class="order-info">
                            <div class="info-item">
                                <span class="info-label">ORDER DATE</span>
                                <span class="info-value">
                                    <?php echo date('F j, Y - g:i A', strtotime($order['order_date'])); ?>
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">TOTAL AMOUNT</span>
                                <span class="info-value">LKR <?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">PAYMENT METHOD</span>
                                <span class="info-value"><?php echo $order['payment_method'] ? $order['payment_method'] : 'Not specified'; ?></span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">DELIVERY ADDRESS</span>
                                <span class="info-value"><?php echo $order['delivery_address'] ? $order['delivery_address'] : 'Not specified'; ?></span>
                            </div>
                        </div>

                        <!-- View items button -->
                        <button class="view-btn" onclick="showItems(<?php echo $order['order_id']; ?>)">
                            View Order Items
                        </button>

                        <!-- Hidden items list -->
                        <div class="order-items-list" id="items-<?php echo $order['order_id']; ?>">
                            <h3>Order Items</h3>
                            <?php
                            // Get items for this order
                            $items_sql = "SELECT oi.quantity, oi.price, b.Name 
                                         FROM order_items oi
                                         JOIN bakeryitems b ON oi.item_id = b.item_id
                                         WHERE oi.order_id = ?";
                            $items_stmt = $conn->prepare($items_sql);
                            $items_stmt->bind_param("i", $order['order_id']);
                            $items_stmt->execute();
                            $items_result = $items_stmt->get_result();
                            ?>

                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = $items_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['Name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>LKR <?php echo number_format($item['price'], 2); ?></td>
                                            <td>LKR <?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <?php $items_stmt->close(); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <h2>Sweet Bakery</h2>
            <p>Crafting fresh, artisanal baked goods daily with the finest ingredients and traditional techniques.</p>

            <h2>Contact Info</h2>
            <p>123 Baker Street <br> Colombo District <br> City, State 12345</p>
            <p>0771234567</p>
            <p>sweetbakery12@gmail.com</p>

            <h2>Quick Links</h2>
            <a href="Home.php">Home</a><br>
            <a href="Menu.php">Menu</a><br>
            <a href="About.php">About Us</a><br>
            <a href="Contact.php">Contact</a>

            <h2>Hours</h2>
            <p>Mon - Fri: 6:00 AM - 8:00 PM <br>
               Saturday: 7:00 AM - 9:00 PM<br>
               Sunday: Closed</p>

            <hr>
            <footer>2025 Sweet Bakery. All rights reserved. Made with love and flour.</footer>
        </div>
    </div>

    <script>
        // Function to show/hide order items
        function showItems(orderId) {
            var itemsList = document.getElementById('items-' + orderId);
            var button = event.target;
            
            // Toggle show/hide
            if (itemsList.classList.contains('show')) {
                itemsList.classList.remove('show');
                button.textContent = 'View Order Items';
            } else {
                itemsList.classList.add('show');
                button.textContent = 'Hide Order Items';
            }
        }
    </script>
    <script>
    // Toggle mobile menu
    function toggleMenu() {
        const navLinks = document.getElementById('navLinks');
        navLinks.classList.toggle('show');
    }

    // Toggle user dropdown on mobile
    function toggleUserMenu(event) {
        if (window.innerWidth <= 832) {
            event.preventDefault();
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const navLinks = document.getElementById('navLinks');
        const menuIcon = document.querySelector('.menu-icon');
        
        if (!navLinks.contains(event.target) && !menuIcon.contains(event.target)) {
            navLinks.classList.remove('show');
        }
    });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>