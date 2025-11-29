<?php
include("functions.php");
$db = new class_functions();
$conn = $db->getConn();

$data = json_decode(file_get_contents('php://input'), true);

$status = $data['status'];
$request_id = $data['request_id'];
$comment = $data['comment'] ?? null;
$faculty_id = $_SESSION['faculty_id']; // Assume faculty is logged in

// Update the status of the request
$sql = "UPDATE gatereq SET status = ? WHERE request_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $status, $request_id);

if ($stmt->execute()) {
    // If the request was rejected, insert the comment
    if ($status == 'rejected' && $comment) {
        $sql = "INSERT INTO comments (request_id, faculty_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $request_id, $faculty_id, $comment);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
