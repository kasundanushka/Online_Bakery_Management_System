<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: sign_in.php");
    exit();
}

// Get user details from database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT user_id, full_name, email, phone, address, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: sign_in.php");
    exit();
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Sweet Bakery</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }

        .profile-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .profile-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
            margin-bottom: 30px;
        }

        .profile-header h1 {
            color: #395b34;
            margin-bottom: 10px;
        }

        .profile-info {
            display: grid;
            gap: 20px;
        }

        .info-row {
            display: flex;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #395b34;
        }

        .info-label {
            font-weight: bold;
            color: #395b34;
            min-width: 150px;
        }

        .info-value {
            color: #333;
            flex: 1;
        }

        .profile-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary {
            background-color: #395b34;
            color: white;
        }

        .btn-primary:hover {
            background-color: #27401f;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            background: #395b34;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #395b34;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
        .sign-in-btn:hover {
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
            <li><a href="cart.php">Cart</a></li>
            <li><a href="logout.php"><button style="background-color: #dc3545; color: white;">Logout</button></a></li>
        </div>
        </ul>
    </nav><br><br>       
            

        <div class="profile-container">
            <a href="Home.php" class="back-link">← Back to Home</a>
            
            <div class="profile-card">
                <div class="profile-header">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h1>My Profile</h1>
                    <p style="color: #666;">Manage your account information</p>
                </div>

                <div class="profile-info">
                    <div class="info-row">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="my_orders.php" class="btn btn-secondary">My Orders</a>
                </div>
            </div>
        </div>

        <div class="footer" style="margin-top: 50px;">
            <hr>
            <footer>2025 Sweet Bakery. All rights reserved. Made with love and flour.</footer>
        </div>
    </div>

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