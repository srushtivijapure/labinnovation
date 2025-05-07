<?php
session_start();
//require_once 'session_check.php';
include 'functions.php';

// Debugging: Uncomment to see errors
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: index.php");
    exit();
}

// Validate inputs
$email = $_POST['user_email'];
$password = $_POST['user_password'];

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Email and password are required.";
    header("Location: index.php");
    exit();
}

// Use prepared statement
$query = "SELECT * FROM student_registration WHERE email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['error'] = "Database error. Please try again later.";
    header("Location: index.php");
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    
    // Verify password (assuming plaintext for now - INSECURE!)
    if ($password === $student['password']) {
        // Set session variables
        $_SESSION['student_id'] = $student['student_id'];
        $_SESSION['student_name'] = $student['full_name'];
        $_SESSION['email'] = $student['email'];
        $_SESSION['loggedin'] = true;
        
        // Debugging output
        error_log("Login successful for student ID: " . $student['student_id']);
        
        // Redirect to dashboard
        header("Location: student-dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Incorrect password.";
    }
} else {
    $_SESSION['error'] = "No account found with that email.";
}

// If we get here, login failed
header("Location: student-dashboard.php");
exit();
?>