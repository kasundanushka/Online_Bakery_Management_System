<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['checkout_error'] = "Please sign in to checkout.";
    header("Location: sign_in.php");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: cart.php");
    exit();
}

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$total = $subtotal;

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, phone, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sweet Bakery</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .checkout-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .checkout-section h2 {
            color: #2d5016;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .order-summary {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .order-summary h2 {
            color: #2d5016;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            margin-top: 15px;
            border-top: 2px solid #f0f0f0;
            font-size: 1.3em;
            font-weight: 700;
            color: #2d5016;
        }

        .place-order-btn {
            width: 100%;
            background: #2d5016;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }

        .place-order-btn:hover {
            background: #1f3810;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .payment-option {
            border: 2px solid #e0e0e0;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
        }

        .payment-option input[type="radio"] {
            margin-right: 8px;
        }

        .payment-option:has(input:checked) {
            border-color: #2d5016;
            background: #f0f7ed;
        }

        @media (max-width: 968px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
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

<div class="checkout-container">
    <div>
        <div class="checkout-section">
            <h2>Delivery Information</h2>
            <form method="POST" action="process_order.php">
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Delivery Address:</label>
                    <textarea name="delivery_address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <h2 style="margin-top: 30px; color: #2d5016;">Payment Method</h2>
                
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Cash on Delivery" checked>
                        Cash on Delivery
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Bank Transfer">
                        Bank Transfer
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Credit Card">
                        Credit Card
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Mobile Payment">
                        Mobile Payment
                    </label>
                </div>


                <button type="submit" class="place-order-btn">Place Order</button>
            </form>
        </div>
    </div>

    <div class="order-summary">
        <h2>Order Summary</h2>
        
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <div class="summary-item">
                <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                <span>LKR <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
        <?php endforeach; ?>

        <div class="summary-total">
            <span>Total</span>
            <span>LKR <?php echo number_format($total, 2); ?></span>
        </div>
    </div>
</div>

<footer class="footer">
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
    <p>2025 Sweet Bakery. All rights reserved. Made with love and flour.</p>
</footer>

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
$stmt->close();
$conn->close();
?>