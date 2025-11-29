<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="css.css">
    <style>
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
            <li><a href="Contact.php" class="active">Contact</a></li>
            <li><a href="cart.php">Cart</a></li>
        

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

    <div class="Contact-intro" style=" margin:50px;">
    <strong style="color:rgb(27, 99, 27); font-size:40px;">Contact Us</strong>
    <p style="font-size:20px;">We'd love to hear from you! Whether you have questions about our products, want to place a special order, or just want to say hello.</p>
    </div>
    
    <section>
    <div class="Contact-intro"style=" margin:50px;">
    <strong style="color:rgb(27, 99, 27); font-size:23px;">Get in Touch</strong><br>
    <span style="font-size:18px;">Visit our bakery, give us a call, or send us a message. We're here to help and would love to discuss how we can make your next event or daily bread experience special.</span>
    </div>
    
    <div class="contact-list">
        <div class="contact">
            <div class="Contact-intro">
                 <strong style="color:rgb(35, 107, 35); font-size:20px;">Visit Us</strong>
                 <p>123 Baker Street<br>
                    Colombo District<br>
                    City, State 12345</p>
            </div>            
        </div>
        
        <div class="contact">
            <div class="Contact-intro">
                 <strong style="color:rgb(35, 107, 35); font-size:20px;">Call Us</strong>
                 <p>0771234567</p>
            </div>
        </div>
        <div class="contact">
            <div class="Contact-intro">
                 <strong style="color:rgb(35, 107, 35); font-size:20px;">Email Us</strong>
                 <p>sweetbakery12@gmail.com</p>
            </div>            
        </div>
        <div class="contact">
            <div class="Contact-intro">
                 <strong style="color:rgb(35, 107, 35); font-size:20px;">Hours</strong>
                 <p>Mon - Fri: 6:00 AM - 8:00 PM<br>
                    Saturday: 7:00 AM - 9:00 PM<br>
                    Sunday: Closed</p>
            </div>            
        </div>       
    </div>
    </section><br><br>

    <div class="contact-list">
    <section>
    <div class="Special_Services">
    <h2>Special Services</h2>
    <ul style="font-size:19px;">
        <li>
            <strong>Custom Cakes & Catering</strong><br>
            <span>Special orders for weddings, birthdays, and corporate events</span>
        </li><br>
        
        <li>
            <strong>Wholesale Orders</strong><br>
            <span>Bulk orders for restaurants, cafes, and businesses</span>
        </li><br>

        <li>
            <strong>Baking Classes</strong><br>
            <span>Learn traditional baking techniques from our master bakers</span>
        </li>
    </ul>
    </div>
    </section>

   
    <div class="from">
    <form action="contact_from_process.php" method="POST">
        <h2 style="color:rgb(33, 105, 33)">Send Us a Message</h2>
        
        <?php
        // Show success message
        if (isset($_SESSION['contact_success'])) {
            echo '<div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #c3e6cb;">';
            echo $_SESSION['contact_success'];
            echo '</div>';
            unset($_SESSION['contact_success']);
        }
        ?>
        
        <label for="name">Full Name*</label><br>
        <input type="text" id="name" name="name" placeholder="Your full name" required><br><br>
        
        <label for="email">Email Address*</label><br>
        <input type="email" id="email" name="email" placeholder="Your@email.com" required><br><br>

        <label for="number">Phone Number</label><br>
        <input type="tel" id="number" name="number" placeholder="(+94) xxxxxxxxxx"><br><br>

        <label for="Subject">Subject*</label><br>
        <select id="Subject" name="Subject" required>
            <option value="" disabled selected>--Select a subject--</option>
            <option value="General Inquiry">General Inquiry</option>
            <option value="Place an Order">Place an Order</option>
            <option value="Catering Request">Catering Request</option>
            <option value="Custom cake Order">Custom cake Order</option>
            <option value="Wholesale Inquiry">Wholesale Inquiry</option>
            <option value="Banking Classes">Banking Classes</option>
            <option value="Feedback">Feedback</option>
        </select><br><br>

        <label for="massage">Message*</label><br>
        <textarea id="massage" name="massage" placeholder="Tell us how we can help you..." style="height:70px;" required></textarea><br><br>
        
        <div class="b-submit">
            <button type="submit" style="background-color: #395b34; color:white;">Send Message</button>
        </div>
    </form>
</div>
    
    <div class="footer" style="width:100%;">
        <h2 >Sweet Bakery</h2>
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
           Sunday:  Closed</p>
        
        <hr>
        <footer>2025 Sweet Bakery. All rights reserved. Made with love and flour.</footer>
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