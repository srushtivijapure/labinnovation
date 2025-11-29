<?php
include("functions.php");
$db = new class_functions();
$conn = $db->getConn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Consider hashing in production
    $enrollmentNo = $_POST['enrollment_no'];
    $department = $_POST['department'];
    $year = $_POST['year'];

    // Insert into users table
    $userType = 'student';
    $stmt = $conn->prepare("INSERT INTO users (user_email, user_password, user_type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $userType);
    if ($stmt->execute()) {
        $userId = $conn->insert_id;

        // Insert into students table
        $stmt2 = $conn->prepare("INSERT INTO students (user_id, full_name, enrollment_no, department, year) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("issss", $userId, $fullName, $enrollmentNo, $department, $year);
        if ($stmt2->execute()) {
            echo "success";
        } else {
            echo "Error inserting into students: " . $stmt2->error;
        }
        $stmt2->close();
    } else {
        echo "Error inserting into users: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
