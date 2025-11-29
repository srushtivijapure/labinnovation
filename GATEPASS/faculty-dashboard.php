

<?php 
include("functions.php");
session_start();

$db = new class_functions();
$conn = $db->getConn();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "
    SELECT s.faculty_id, s.full_name, u.user_email 
    FROM faculty s 
    JOIN users u ON s.user_id = u.user_id 
    WHERE u.user_id = '$user_id'
";
$student_result = mysqli_query($conn, $query);
$student_data = mysqli_fetch_assoc($student_result);

if (!$student_data) {
    echo "Student data not found.";
    exit;
}

$faculty_id = $student_data['faculty_id'];

$query = "SELECT f.full_name, u.user_email 
          FROM faculty f 
          JOIN users u ON f.user_id = u.user_id 
          WHERE f.faculty_id = '$faculty_id'";
          
$result = mysqli_query($conn, $query);
if ($row = mysqli_fetch_assoc($result)) {
    $name = $row['full_name'];
    $email = $row['user_email'];
}
$faculty = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { 
            height: 100vh; 
            width: 250px; 
            background-color: #007bff; 
            color: white; 
            padding: 20px; 
            position: fixed; 
        }
        .sidebar a { 
            color: white; 
            text-decoration: none; 
            display: block; 
            padding: 10px; 
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover, 
        .sidebar a.active { 
            background-color: rgba(255, 255, 255, 0.3); 
            font-weight: bold; 
        }
        .main-content { 
            margin-left: 270px; 
            padding: 20px; 
        }
        .card { 
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1); 
            margin-top: 20px; 
        }
        .section { 
            display: none; 
        }
        .section.active {
            display: block;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Faculty Dashboard</h4>
        <a href="#" class="nav-link active" data-section="pendingRequests">View Pending Requests</a>
        <a href="#" class="nav-link" data-section="approvedRequests">Approved Requests</a>
        <a href="#" class="nav-link" data-section="rejectedRequests">Rejected Requests</a>
        <a href="#" class="nav-link" data-section="profile">Profile</a>
        <a href="logout.php" class="nav-link" id="logoutLink">Logout</a>
    </div>

    <div class="main-content">
        <h2>Welcome, <?php echo $student_data['full_name']; ?></h2>

        <!-- Pending Requests -->
        <div id="pendingRequests" class="card p-4 section active">
            <h4>Pending Requests</h4>
            <table class="table table-bordered">
                <thead><tr><th>Student Name</th><th>Year</th><th>Reason</th> <th>Actions</th></tr></thead>
                <tbody>
                    <?php
                   

                    $query = "SELECT s.full_name, gp.year, gp.reason , gp.request_id
                              FROM gatereq gp 
                              JOIN students s ON gp.student_id = s.student_id 
                              WHERE gp.status = 0";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['full_name']}</td>
                                <td>{$row['year']}</td>
                                <td>{$row['reason']}</td>
                                 <td>
                            <button class='btn btn-success btn-sm' onclick='updateStatus({$row['request_id']}, 1)'>Approve</button>
                               <button class='btn btn-danger btn-sm' onclick='updateStatus({$row['request_id']}, 2)'>Reject</button>
                        </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Approved Requests -->
        <div id="approvedRequests" class="card p-4 section active">
            <h4>Approved Requests</h4>
            <table class="table table-bordered">
                <thead><tr><th>Student Name</th><th>Year</th><th>Reason</th></tr></thead>
                <tbody>
                    <?php
                    $query = "SELECT s.full_name, gp.year, gp.reason 
                              FROM gatereq gp 
                              JOIN students s ON gp.student_id = s.student_id 
                              WHERE gp.status = 1";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['full_name']}</td>
                                <td>{$row['year']}</td>
                                <td>{$row['reason']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Rejected Requests -->
        <div id="rejectedRequests" class="card p-4 section active">
            <h4>Rejected Requests</h4>
            <table class="table table-bordered">
                <thead><tr><th>Student Name</th><th>Year</th><th>Reason</th></tr></thead>
                <tbody>
                    <?php
                    $query = "SELECT s.full_name, gp.year, gp.reason, c.comment 
                              FROM gatereq gp 
                              JOIN students s ON gp.student_id = s.student_id 
                              LEFT JOIN comments c ON gp.request_id = c.request_id 
                              WHERE gp.status = 2";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['full_name']}</td>
                                <td>{$row['year']}</td>
                                <td>{$row['reason']}</td>
                             
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Profile -->
        <div id="profile" class="card p-4 section active">
            <h4>Profile</h4>
            <form id="profileForm">
                <div class="mb-3">
                    <label for="facultyName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="facultyName" name="facultyname" value="<?php echo $student_data['full_name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $student_data['user_email']; ?>" required>
                </div>
                <div id="message"></div>
                <button type="button" id="updateProfileButton" class="btn btn-primary">Update Profile</button>
            </form>
        </div>


    </div>

    <script>
            // Profile update functionality
            document.getElementById('updateProfileButton').addEventListener('click', function() {
                const form = document.getElementById('profileForm');
                const formData = new FormData(form);
                
                fetch('update_facultyprofile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    const msg = document.getElementById('message');
                    msg.innerHTML = `<div class="alert ${data.status === 'success' ? 'alert-success' : 'alert-danger'}">${data.message}</div>`;
                    if (data.status === 'success') {
                        setTimeout(() => {
                            msg.innerHTML = '';
                        }, 3000);
                    }
                })
                .catch(err => {
                    document.getElementById('message').innerHTML = 
                        '<div class="alert alert-danger">Error: ' + err + '</div>';
                });
            });
            
            // Initialize with first section active
            switchSection('pendingRequests');
        
    </script>


<script>
    function updateStatus(requestId, status) {
        
        fetch('update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `request_id=${requestId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                location.reload(); // Refresh to reflect updated list
            }
        })
        .catch(error => {
            alert('Error updating request: ' + error);
        });
    }
</script>

</body>
</html>