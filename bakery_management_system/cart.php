<?php
session_start();
include('db.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_quantity'])) {
        $product_name = $_POST['product_name'];
        $new_quantity = (int)$_POST['quantity'];
        
        if ($new_quantity > 0) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['name'] == $product_name) {
                    $_SESSION['cart'][$key]['quantity'] = $new_quantity;
                    break;
                }
            }
        }
    }
    
    if (isset($_POST['remove_item'])) {
        $product_name = $_POST['product_name'];
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['name'] == $product_name) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
    }
    
    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
    }
    
    header("Location: cart.php");
    exit();
}


$subtotal = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Sweet Bakery</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .cart-page {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .cart-header h1 {
            color: #2d5016;
            font-size: 2.5em;
            margin: 0;
        }

        .cart-header p {
            color: #666;
            margin: 5px 0 0 0;
        }

        .continue-shopping {
            background: white;
            border: 2px solid #2d5016;
            color: #2d5016;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .continue-shopping:hover {
            background: #2d5016;
            color: white;
        }

        .cart-items-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .cart-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .cart-items-header h2 {
            color: #2d5016;
            margin: 0;
        }

        .clear-cart-btn {
            background: transparent;
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .clear-cart-btn:hover {
            background: #dc3545;
            color: white;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }

        .cart-item-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .cart-item-name {
            color: #2d5016;
            font-size: 1.2em;
            font-weight: 600;
            margin: 0;
        }

        .cart-item-price {
            color: #666;
            font-size: 0.95em;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 2px solid #2d5016;
            background: white;
            color: #2d5016;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .quantity-btn:hover {
            background: #2d5016;
            color: white;
        }

        .quantity-display {
            font-size: 16px;
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 15px;
        }

        .cart-item-total {
            font-size: 1.3em;
            font-weight: 700;
            color: #2d5016;
        }

        .remove-btn {
            background: transparent;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 24px;
            padding: 5px;
            transition: all 0.3s;
        }

        .remove-btn:hover {
            transform: scale(1.2);
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
            margin: 0 0 25px 0;
            font-size: 1.8em;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            color: #666;
            font-size: 1.05em;
        }

        .summary-row.total {
            border-top: 2px solid #f0f0f0;
            margin-top: 15px;
            padding-top: 20px;
            font-size: 1.3em;
            font-weight: 700;
            color: #2d5016;
        }

        .checkout-btn {
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
            transition: all 0.3s;
        }

        .checkout-btn:hover {
            background: #1f3810;
            transform: translateY(-2px);
        }

        .checkout-note {
            text-align: center;
            color: #999;
            font-size: 0.9em;
            margin-top: 15px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-cart h2 {
            color: #2d5016;
            margin-bottom: 15px;
        }

        @media (max-width: 832px) {
            .cart-page {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                position: static;
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

<div class="cart-page">
    <div>
        <div class="cart-header">
            <div>
                <h1>Shopping Cart</h1>
                <p><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?> item<?php echo (isset($_SESSION['cart']) && count($_SESSION['cart']) != 1) ? 's' : ''; ?> in your cart</p>
            </div>
            <a href="Menu.php" class="continue-shopping">← Continue Shopping</a>
        </div>

        <div class="cart-items-section">
            <?php if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0): ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <p>Add some delicious items from our menu!</p>
                    <a href="Menu.php" class="continue-shopping" style="margin-top: 20px;">Browse Menu</a>
                </div>
            <?php else: ?>
                <div class="cart-items-header">
                    <h2>Cart Items</h2>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="clear_cart" class="clear-cart-btn" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
                    </form>
                </div>

                <?php foreach ($_SESSION['cart'] as $item): 
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                        
                        <div class="cart-item-details">
                            <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="cart-item-price">LKR <?php echo number_format($item['price'], 2); ?> each</p>
                            
                            <div class="quantity-controls">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                                    <button type="submit" name="update_quantity" class="quantity-btn">−</button>
                                </form>
                                
                                <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                    <button type="submit" name="update_quantity" class="quantity-btn">+</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="cart-item-actions">
                            <span class="cart-item-total">LKR <?php echo number_format($item_total, 2); ?></span>
                            <form method="POST">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                <button type="submit" name="remove_item" class="remove-btn" title="Remove item">🗑</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): 
        $total = $subtotal;
    ?>
        <div class="order-summary">
            <h2>Order Summary</h2>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span>LKR <?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery Fee</span>
                <span>Free</span>
            </div>
            
            <div class="summary-row total">
                <span>Total</span>
                <span>LKR <?php echo number_format($total, 2); ?></span>
            </div>
            
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <!-- User is logged in - Go to checkout -->
            <a href="checkout.php">
                <button class="checkout-btn">Proceed to Checkout</button>
            </a>
        <?php else: ?>
            <!-- User is not logged in - Go to sign in -->
            <a href="sign_in.php">
                <button class="checkout-btn">Sign In to Checkout</button>
            </a>
        <?php endif; ?>
        
        <p class="checkout-note">Secure checkout with multiple payment options</p>
    </div>
<?php endif; ?>
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