<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $errors = array();
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long!";
    }
    
    $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already registered! Please use a different email.";
    }
    
    if (count($errors) > 0) {
        $_SESSION['signup_error'] = implode("<br>", $errors);
        header("Location: sign_up.php");  
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $password, $phone, $address);
    
    if ($stmt->execute()) {
        $_SESSION['signup_success'] = "Registration successful! You can now log in.";
        header("Location: sign_in.php");
        exit();
    } else {
        $_SESSION['signup_error'] = "Registration failed! Please try again.";
        header("Location: sign_up.php");  
        exit();
    }
    
    $stmt->close();
    $conn->close();
    
} else {
    header("Location: sign_up.php");  
    exit();
}
?>