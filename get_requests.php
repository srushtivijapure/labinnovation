<?php
include("functions.php");

$db = new class_functions();
$conn = $db->getConn();

// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

$requests = [
    "pending" => [],
    "approved" => [],
    "rejected" => []
];

$sql = "SELECT gr.*, s.full_name, s.year 
        FROM gatereq gr 
        JOIN users s ON gr.student_id = s.user_id
        WHERE gr.status IN ('pending', 'approved', 'rejected')";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $entry = [
        "name" => $row["full_name"],
        "year" => $row["year"],
        "reason" => $row["reason"],
        "status" => $row["status"],
        "request_id" => $row["request_id"]
    ];
    $requests[$row["status"]][] = $entry;
}

header('Content-Type: application/json');
echo json_encode($requests);
?>
