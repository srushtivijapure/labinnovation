<?php
include("functions.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$db = new class_functions();
$conn = $db->getConn();
$user_id = $_SESSION['user_id'];

// Get student_id from user_id
$query = "SELECT student_id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Student record not found']);
    exit();
}

$data = $result->fetch_assoc();
$student_id = $data['student_id'];

// Sanitize input
$full_name = trim($_POST['studentName'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($full_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Name and Email cannot be empty']);
    exit();
}

// Update students table
$update_student = "UPDATE students SET full_name = ? WHERE student_id = ?";
$stmt1 = $conn->prepare($update_student);
$stmt1->bind_param("si", $full_name, $student_id);

// Update users table
$update_user = "UPDATE users SET user_email = ? WHERE user_id = ?";
$stmt2 = $conn->prepare($update_user);
$stmt2->bind_param("si", $email, $user_id);

// Execute and respond
if ($stmt1->execute() && $stmt2->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
?>
