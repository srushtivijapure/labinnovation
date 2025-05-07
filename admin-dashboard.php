<?php 
include("functions.php");
session_start();

$db = new class_functions();
$conn = $db->getConn();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { height: 100vh; width: 250px; background-color: #343a40; color: white; padding: 20px; position: fixed; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; }
        .sidebar a:hover { background-color: rgba(255, 255, 255, 0.2); }
        .main-content { margin-left: 270px; padding: 20px; }
        .card { box-shadow: 0px 4px 8px rgba(0,0,0,0.1); margin-top: 20px; }
        .d-none { display: none; }
    </style>
</head>
<body>
    
    <div class="sidebar">
        <h4>Admin Dashboard</h4>
        <a href="#studentRecords" onclick="showSection('studentRecords')">Student Records</a>
        <a href="#facultyRecords" onclick="showSection('facultyRecords')">Faculty Records</a>
        <a href="#requestLogs" onclick="showSection('requestLogs')">Request Logs</a>
        <a href="#analytics" onclick="showSection('analytics')">System Analytics</a>
        <a href="#logout" onclick="logout()">Logout</a>
    </div>

    <div class="main-content">
        <h2>Welcome, Admin</h2>
        
        <!-- Student Records -->
        <div id="studentRecords" class="card p-4 active ">
            <h4>Student Records</h4>
            <table class="table table-bordered">
                <thead><tr><th>Name</th><th>Year</th><th>Email</th></tr></thead>
                <tbody>
                    <?php
                   $res = $conn->query("
                   SELECT s.full_name, s.year, u.user_email 
                   FROM students s 
                   JOIN users u ON s.user_id = u.user_id
               ");
               
                    while ($row = $res->fetch_assoc()) {
                        echo "<tr><td>{$row['full_name']}</td><td>{$row['year']}</td><td>{$row['user_email']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Faculty Records -->
        <div id="facultyRecords" class="card p-4">
            <h4>Faculty Records</h4>
            <table class="table table-bordered">
                <thead><tr><th>Name</th><th>Department</th><th>Email</th></tr></thead>
                <tbody>
                    <?php
                    $res = $conn->query("
                    SELECT f.full_name, f.department, u.user_email 
                    FROM faculty f 
                    JOIN users u ON f.user_id = u.user_id
                ");
                
                    while ($row = $res->fetch_assoc()) {
                        echo "<tr><td>{$row['full_name']}</td><td>{$row['department']}</td><td>{$row['user_email']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Request Logs -->
        <div id="requestLogs" class="card p-4 ">
            <h4>Request Logs</h4>
            <table class="table table-bordered">
                <thead><tr><th>Student Name</th><th>Date</th><th>Reason</th><th>Status</th></tr></thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT s.full_name, g.request_date, g.reason, g.status FROM gatereq g JOIN students s ON g.request_id = s.student_id");
                    while ($row = $res->fetch_assoc()) {
                        echo "<tr><td>{$row['full_name']}</td><td>{$row['request_date']}</td><td>{$row['reason']}</td><td>{$row['status']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- System Analytics -->
        <div id="analytics" class="card p-4 ">
            <h4>System Analytics</h4>
            <?php
            $total = $conn->query("SELECT COUNT(*) AS total FROM gatereq")->fetch_assoc()['total'];
            $approved = $conn->query("SELECT COUNT(*) AS approved FROM gatereq WHERE status='Approved'")->fetch_assoc()['approved'];
            $rejected = $conn->query("SELECT COUNT(*) AS rejected FROM gatereq WHERE status='Rejected'")->fetch_assoc()['rejected'];
            ?>
            <p>Total Requests: <span id="totalRequests"><?= $total ?></span></p>
            <p>Approved Requests: <span id="approvedRequests"><?= $approved ?></span></p>
            <p>Rejected Requests: <span id="rejectedRequests"><?= $rejected ?></span></p>
        </div>


    </div>

    <script>
        /*function showSection(sectionId) {
            document.querySelectorAll('.card').forEach(card => card.classList.add('d-none'));
            document.getElementById(sectionId).classList.remove('d-none');
        }*/
        
        function logout() {
            alert('Logging out...');
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>
