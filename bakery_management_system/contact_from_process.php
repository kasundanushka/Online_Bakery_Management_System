<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['number']);
    $subject = trim($_POST['Subject']);
    $message = trim($_POST['massage']);
    
    // Validation
    $errors = array();
    
    if (empty($name)) {
        $errors[] = "Name is required!";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }
    
    if (empty($subject) || $subject == "Select a subject") {
        $errors[] = "Please select a subject!";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required!";
    }
    
    // If there are errors, redirect back with error message
    if (count($errors) > 0) {
        $_SESSION['contact_error'] = implode("<br>", $errors);
        header("Location: Contact.php");
        exit();
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email,phone_number, subject, message) VALUES (?, ?, ?, ?,?)");
    $stmt->bind_param("sssss", $name, $email,$phone, $subject, $message);
    
    if ($stmt->execute()) {
        // Success
        $_SESSION['contact_success'] = "Thank you for contacting us! We'll get back to you soon.";
        header("Location: Contact.php");
        exit();
    } 
    
    $stmt->close();
    $conn->close();
    
} else {
    // If someone tries to access this page directly
    header("Location: Contact.php");
    exit();
}
?>