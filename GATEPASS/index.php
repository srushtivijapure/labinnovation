<?php 

include("functions.php");
$db = new class_functions();
$conn = $db->getConn();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['user_email'];
    $password = trim($_POST['user_password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['user_password']) { // ideally use password_hash in production
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_type'] = $user['user_type'];

            switch ($user['user_type']) {
                case 'student':
                    header('Location: student-dashboard.php');
                    break;
                case 'faculty':
                    header('Location: faculty-dashboard.php');
                    break;
                case 'admin':
                    header('Location: admin-dashboard.php');
                    break;
                default:
                    echo "Invalid role.";
            }
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #eef2f3; }
        .card { margin-top: 50px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); }
        .navbar { background-color: #007bff; }
        .navbar-brand, .nav-link { color: white !important; }
        .container { max-width: 800px; }
        .btn { border-radius: 5px; }
        .logo {
            margin-left:60px;
        }
    </style>

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">N B Navale,Sinhagad College of Engineering,Solapur </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="adminlogin.php">Admin Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <!--<li class="nav-item"><a class="nav-link" href="#" onclick="showRegisterOptions()">Register</a></li>-->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Page -->
    <div class="container d-flex justify-content-center">
        <div class="card p-4" style="width: 400px;" id="home">
            <h3 class="text-center">Login</h3>
            <form id="loginForm" method="POST" action="index.php">
            <centre><img src="nbnscoe.jpg" alt="College Logo" class="logo"></centre>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="user_email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="user_password" required>
    </div>
    <input type="hidden" id="userType" name="user_type" value=""> <!-- optional: for tracking role -->
    <div class="d-flex justify-content-between mb-3">
        <button type="submit" class="btn btn-primary w-50 me-2" onclick="setUserType('student')">Login as Student</button>
        <button type="submit" class="btn btn-secondary w-50" onclick="setUserType('faculty')">Login as Faculty</button>
    </div>
    <div class="text-center">
        <a href="#" class="text-decoration-none">Forgot Password?</a> | 
        <a href="#" onclick="showRegisterOptions()" class="text-decoration-none">Register</a>
    </div>
</form>

        </div>
    </div>

    <!-- Register Options -->
    <div class="container d-flex justify-content-center mt-5 d-none" id="registerOptions">
        <div class="card p-4 text-center" style="width: 400px;">
            <h3>Select Registration Type</h3>
            <button class="btn btn-primary w-100 mt-3" onclick="showRegisterForm('student')">Register as Student</button>
            <button class="btn btn-secondary w-100 mt-3" onclick="showRegisterForm('faculty')">Register as Faculty</button>
        </div>
    </div>

    <!-- Student Register Page -->
    <div class="container d-flex justify-content-center mt-5 d-none" id="studentRegister">
        <div class="card p-4" style="width: 400px;">
            <h3 class="text-center">Student Registration</h3>
            <form>
                <div class="mb-3">
                    <label for="enrollmentno" class="form-label">Enrollment no</label>
                    <input type="text" class="form-control" id="enrollmentno" required>
                </div>
                <div class="mb-3">
                    <label for="studentName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="studentName" required>
                </div>
                <div class="mb-3">
                    <label for="studentEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="studentEmail" required>
                </div>
                <div class="mb-3">
                    <label for="studentPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="studentPassword" required>
                </div>
                <div class="mb-3">
                    <label for="studentDept" class="form-label">Department</label>
                    <input type="text" class="form-control" id="studentDept" required>
                </div>
                <div class="mb-3">
                    <label for="studentYear" class="form-label">Studying Year</label>
                    <input type="text" class="form-control" id="studentYear" required>
                </div>
                <button type="button" class="btn btn-success w-100" onclick="registerUser('student')">Register</button>
            </form>
        </div>
    </div>

    <!-- Faculty Register Page -->
    <div class="container d-flex justify-content-center mt-5 d-none" id="facultyRegister">
        <div class="card p-4" style="width: 400px;">
            <h3 class="text-center">Faculty Registration</h3>
            <form>
                <div class="mb-3">
                    <label for="facultyName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="facultyName" required>
                </div>
                <div class="mb-3">
                    <label for="facultyEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="facultyEmail" required>
                </div>
                <div class="mb-3">
                    <label for="facultyPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="facultyPassword" required>
                </div>
                <div class="mb-3">
                    <label for="facultyDept" class="form-label">Department</label>
                    <input type="departmet" class="form-control" id="facultyDept" required>
                </div>
                <div class="mb-3">
                    <label for="facultyTGBatch" class="form-label">TG Batch</label>
                    <input type="TGBatch" class="form-control" id="facultyTGBatch" required>
                </div>
                <button type="button" class="btn btn-success w-100" onclick="registerUser('faculty')">Register</button>
            </form>
        </div>
    </div>
    
    <div class="container mt-5" id="contact">
   <h2>Contact Us</h2>
        <p>If you have any queries, reach out to us at:</p>
        <p>Email: support@college.com</p>
        <p>Phone: +91 9876543210</p>
    </div>

    <script>
        function showRegisterOptions() {
            document.getElementById('registerOptions').classList.remove('d-none');
            document.getElementById('studentRegister').classList.add('d-none');
            document.getElementById('facultyRegister').classList.add('d-none');
        }

        function showRegisterForm(type) {
            document.getElementById('registerOptions').classList.add('d-none');
            if (type === 'student') {
                document.getElementById('studentRegister').classList.remove('d-none');
            } else {
                document.getElementById('facultyRegister').classList.remove('d-none');
            }
        }

    function registerUser(type) {
    if (type === 'student') {
        const data = {
            full_name: document.getElementById("studentName").value,
            email: document.getElementById("studentEmail").value,
            password: document.getElementById("studentPassword").value,
            enrollment_no: document.getElementById("enrollmentno").value,
            department: document.getElementById("studentDept").value,
            year: document.getElementById("studentYear").value
        };

        fetch('register_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data).toString()
        })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === "success") {
                alert("Student Registration Successful! Please Login.");
                location.href = "index.php"; // redirect to login
            } else {
                alert("Registration failed: " + result);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    if (type === 'faculty') {
        const name = document.getElementById("facultyName").value;
        const email = document.getElementById("facultyEmail").value;
        const password = document.getElementById("facultyPassword").value;
        const department = document.getElementById("facultyDept").value;
        const tg_batch = document.getElementById("facultyTGBatch").value;

        const formData = new FormData();
        formData.append("name", name);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("department", department);
        formData.append("tg_batch", tg_batch);

        fetch('register_faculty.php', {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(response => {
            if (response.trim() === "success") {
                alert("Faculty Registration Successful! Please Login.");
                window.location.href = "index.php";
            } else {
                alert("Registration failed: " + response);
            }
        });
    }
}



    </script>

<script>
    function setUserType(type) {
        document.getElementById("userType").value = type;
    }
</script>

</body>
</html>
 