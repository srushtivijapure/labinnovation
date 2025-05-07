<?php
include("functions.php");
$db = new class_functions();
$conn = $db->getConn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dept = $_POST['department'];
    $tg_batch = $_POST['tg_batch'];
    $user_type = 'faculty';

    // First, insert into users table
    $stmt1 = $conn->prepare("INSERT INTO users (user_email, user_password, user_type) VALUES (?, ?, ?)");
    $stmt1->bind_param("sss", $email, $password, $user_type);

    if ($stmt1->execute()) {
        $user_id = $stmt1->insert_id;

        // Insert into faculty table
        $stmt2 = $conn->prepare("INSERT INTO faculty (user_id, full_name, department, tg_batch) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $user_id, $name, $dept, $tg_batch);

        if ($stmt2->execute()) {
            echo "success";
        } else {
            echo "Error inserting into faculty table: " . $stmt2->error;
        }

        $stmt2->close();
    } else {
        echo "Error inserting into users table: " . $stmt1->error;
    }

    $stmt1->close();
    $conn->close();
}
?>
