<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Sweet Bakery</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: rgb(152, 190, 160);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-weight: bold;
            font-size: 50px;
            text-align: center;
            margin-bottom: 10px;
        }

        h4 {
            font-weight: normal;
            text-align: center;
            margin-bottom: 30px;
        }

        h4 a {
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }

        h4 a:hover {
            text-decoration: underline;
        }

        label {
            font-weight: bold;
            font-size: 20px;
            display: block;
            margin-bottom: 8px;
        }

        input {
            padding: 12px;
            margin-bottom: 20px;
            width: 100%;
            border: 2px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #28a745;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            text-align: center;
            font-weight: 500;
        }

        .back-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-home a {
            color: #666;
            text-decoration: none;
        }

        .back-home a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
<div class="signup-container">
    <form method="POST" action="signin_process.php">
        
        <h1>Log in</h1>
        <h4>New to this site? <a href="sign_up.html">Sign Up</a></h4>

        <?php
        if (isset($_SESSION['login_error'])) {
            echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
            unset($_SESSION['login_error']);
        }
        
        if (isset($_SESSION['signup_success'])) {
            echo '<div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; text-align: center; font-weight: 500;">' . $_SESSION['signup_success'] . '</div>';
            unset($_SESSION['signup_success']);
        }
        ?>

        <label for="email">Username:</label>
        <input type="email" name="email" id="email" placeholder="Username/email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Password" required>

        <button type="submit">Sign In</button>
        
    </form>

    <div class="back-home">
        <a href="Home.php">← Back to Home</a>
    </div>
</div>

</body>
</html>