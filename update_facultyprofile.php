<?php
include("functions.php");
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

$facultyname = trim($_POST['facultyname'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($facultyname) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Name and Email cannot be empty."]);
    exit;
}

$db = new class_functions();
$conn = $db->getConn();

// Update users table
$updateUser = "UPDATE users SET user_email = ? WHERE user_id = ?";
$stmt1 = $conn->prepare($updateUser);
$stmt1->bind_param("si", $email, $user_id);
$stmt1->execute();

// Update faculty table
$updateFaculty = "UPDATE faculty SET full_name = ? WHERE user_id = ?";
$stmt2 = $conn->prepare($updateFaculty);
$stmt2->bind_param("si", $facultyname, $user_id);
$stmt2->execute();

echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
?>
