<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both email and password!";
        header("Location: sign_in.php");
        exit();
    }
    
    // **CRITICAL UPDATE: Select the is_admin column**
    $stmt = $conn->prepare("SELECT user_id, full_name, email, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Compare plain text passwords directly (STILL NOT SECURE - **USE password_verify() IN PRODUCTION**)
        if ($password === $user['password']) {
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            // **CRITICAL UPDATE: Set is_admin session variable**
            $_SESSION['is_admin'] = (int)$user['is_admin']; 

            // **CRITICAL UPDATE: Admin Redirection**
            if ($_SESSION['is_admin'] == 1) {
                header("Location: admin_panel.php"); // Redirect admin to admin panel
            } else {
                header("Location: Home.php"); // Redirect regular user to home
            }
            exit();
            
        } else {
            $_SESSION['login_error'] = "Incorrect password! Please try again.";
            header("Location: sign_in.php");
            exit();
        }
        
    } else {
        $_SESSION['login_error'] = "Email not found! Please sign up first.";
        header("Location: sign_in.php");
        exit();
    }
    
    $stmt->close();
    $conn->close();
    
} else {
    header("Location: sign_in.php");
    exit();
}
?>