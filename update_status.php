<?php
include("functions.php");
session_start();

$db = new class_functions();
$conn = $db->getConn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Get faculty ID from session
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $faculty_query = "SELECT faculty_id FROM faculty WHERE user_id = '$user_id'";
    $faculty_result = mysqli_query($conn, $faculty_query);
    $faculty_data = mysqli_fetch_assoc($faculty_result);
    $faculty_id = $faculty_data['faculty_id'] ?? null;

    if (!$faculty_id) {
        echo json_encode(['status' => 'error', 'message' => 'Faculty ID not found.']);
        exit;
    }

    // Update status in gatereq table
    $update = "UPDATE gatereq SET status = '$status' WHERE request_id = '$request_id'";
    if (mysqli_query($conn, $update)) {
        // Insert comment only if rejected
        if ($status == 2 && !empty($comment)) {
            $comment = mysqli_real_escape_string($conn, $comment);
            $insert_comment = "INSERT INTO comments (request_id, faculty_id, comment) VALUES ('$request_id', '$faculty_id', '$comment')";
            mysqli_query($conn, $insert_comment);
        }

        echo json_encode(['status' => 'success', 'message' => 'Request updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }
}
?>
