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
    SELECT s.student_id, s.full_name, u.user_email 
    FROM students s 
    JOIN users u ON s.user_id = u.user_id 
    WHERE u.user_id = '$user_id'
";
$student_result = mysqli_query($conn, $query);
$student_data = mysqli_fetch_assoc($student_result);

if (!$student_data) {
    echo "Student data not found.";
    exit;
}

$student_id = $student_data['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['year'])) {
    $year = $_POST['year'];
    $teacher = $_POST['teacher'];
    $coordinator = $_POST['classcordinator'];
    $hod = $_POST['hod'];
    $reason = $_POST['reason'];

    $insert_query = "INSERT INTO gatereq (student_id, year, teacher, classcordinator, hod, reason, status) 
              VALUES ('$student_id', '$year', '$teacher', '$coordinator', '$hod', '$reason', 0)";
    if ($conn->query($insert_query) === TRUE) {
        echo "<script>alert('Gate Pass Request Submitted Successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: rgb(169, 191, 214); }
        .sidebar {
            height: 100vh; width: 250px; background-color: #007bff; color: white; padding: 20px; position: fixed;
        }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; }
        .sidebar a:hover { background-color: rgba(255, 255, 255, 0.2); }
        .main-content { margin-left: 270px; padding: 20px; }
        .card { box-shadow: 0px 4px 8px rgba(0,0,0,0.1); margin-top: 20px; }
        .d-none { display: none; }
    </style>
</head>
<body>
<div class="sidebar">
    <h4>Student Dashboard</h4>
    <a href="#applyPass" onclick="showSection('applyPass')">Apply for Gate Pass</a>
    <a href="#viewStatus" onclick="showSection('viewStatus')">View Status</a>
    <a href="#pastRequests" onclick="showSection('pastRequests')">Past Requests</a>
    <a href="#profileSettings" onclick="showSection('profileSettings')">Profile Settings</a>
    <a href="#logout" onclick="logout()">Logout</a>
</div>

<div class="main-content">
    <h2>Welcome, <?php echo htmlspecialchars($student_data['full_name']); ?></h2>

    <!-- Apply for Gate Pass -->
    <div id="applyPass" class="card p-4">
        <h4>Apply for Gate Pass</h4>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="year" class="form-label">Select Year</label>
                <select class="form-control" name="year" required>
                    <option value="" selected disabled>Select Year</option>
                    <option>Second Year</option>
                    <option>Third Year</option>
                    <option>Fourth Year</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="teacher" class="form-label">Teacher Guardian</label>
                <select class="form-control" name="teacher" required>
                    <option selected disabled>Select Teacher</option>
                    <option>Prof.U.S.Gatkul</option>
                    <option>Prof.A.M.Gunje</option>
                    <option>Prof.A.D.Ruikar</option>
                    <option>Prof.A.G.Gund</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Class Coordinator</label>
                <select class="form-control" name="classcordinator" required>
                    <option selected disabled>Select Coordinator</option>
                    <option>Prof.U.S.Gatkul</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Head of Department</label>
                <input type="text" class="form-control" name="hod" value="Prof.Harish Gurme" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Reason for Exit</label>
                <input type="text" class="form-control" name="reason" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>

    <!-- View Status -->
    <div id="viewStatus" class="card p-4 d-none">
        <h4>View Status of Requests</h4>
        <table class="table table-bordered">
            <thead><tr><th>Reason</th><th>Status</th></tr></thead>
            <tbody>
                <?php
                $gp_query = "SELECT reason, status FROM gatereq WHERE student_id = '$student_id' ORDER BY request_date DESC";
                $gp_result = mysqli_query($conn, $gp_query);
                if (mysqli_num_rows($gp_result) > 0):
                    while ($row = mysqli_fetch_assoc($gp_result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['reason']) ?></td>
                            <td>
                                <?php
                                    switch ($row['status']) {
                                        case 0: echo '<span class="badge bg-warning text-dark">Pending</span>'; break;
                                        case 1: echo '<span class="badge bg-success">Approved</span>'; break;
                                        case 2: echo '<span class="badge bg-danger">Rejected</span>'; break;
                                        default: echo '<span class="badge bg-secondary">Unknown</span>';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="2">No gate pass requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Past Requests -->
    <div id="pastRequests" class="card p-4 d-none">
        <h4>Past Requests</h4>
        <table class="table table-bordered">
            <thead><tr><th>Reason</th><th>Status</th><th>Request Date</th></tr></thead>
            <tbody>
                <?php
                $past_query = "SELECT reason, status, request_date FROM gatereq WHERE student_id = ? ORDER BY request_date DESC";
                $stmt = $conn->prepare($past_query);
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $past_result = $stmt->get_result();

                if ($past_result->num_rows > 0):
                    while ($row = $past_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['reason']) ?></td>
                            <td>
                                <?php
                                    switch ($row['status']) {
                                        case 0: echo '<span class="badge bg-warning text-dark">Pending</span>'; break;
                                        case 1: echo '<span class="badge bg-success">Approved</span>'; break;
                                        case 2: echo '<span class="badge bg-danger">Rejected</span>'; break;
                                        case 3: echo '<span class="badge bg-secondary">In Progress</span>'; break;
                                        case 4: echo '<span class="badge bg-info">On Hold</span>'; break;
                                        default: echo '<span class="badge bg-secondary">Unknown</span>';
                                    }
                                ?>
                            </td>
                            <td><?= date('d-m-Y', strtotime($row['request_date'])) ?></td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="3">No past requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Profile Settings -->
    <div id="profileSettings" class="card p-4 d-none">
        <h4>Profile Settings</h4>
        <form id="profileForm">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="studentName" value="<?= htmlspecialchars($student_data['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($student_data['user_email']) ?>" required>
            </div>
            <button type="button" id="updateProfileButton" class="btn btn-primary">Update Profile</button>
        </form>
        <div id="message"></div>
    </div>
</div>

<script>
function showSection(id) {
    document.querySelectorAll('.card').forEach(el => el.classList.add('d-none'));
    document.getElementById(id).classList.remove('d-none');
}

function logout() {
    alert('Logging out...');
    window.location.href = 'index.php';
}

document.getElementById('updateProfileButton').addEventListener('click', function() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('message');
        msg.innerHTML = `<div class="alert ${data.status === 'success' ? 'alert-success' : 'alert-danger'}">${data.message}</div>`;
        if (data.status === 'success') setTimeout(() => location.reload(), 1500);
    })
    .catch(err => {
        document.getElementById('message').innerHTML = '<div class="alert alert-danger">Error: ' + err + '</div>';
    });
});
</script>
</body>
</html>
