<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0,123,255,.25);
        }
        .btn-login {
            background-color: #007bff;
            border: none;
            padding: 10px;
        }
        .btn-login:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2>Admin Login</h2>
        </div>
        <form id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div id="loginMessage" class="mb-3"></div>
            <button type="submit" class="btn btn-primary btn-login w-100">Login</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('loginMessage');
            
            // Static credentials
            const adminUsername = "admin";
            const adminPassword = "admin123";
            
            // Clear previous messages
            messageDiv.innerHTML = '';
            
            if (username === adminUsername && password === adminPassword) {
                // Successful login
                messageDiv.innerHTML = '<div class="alert alert-success">Login successful! Redirecting...</div>';
                
                // Store login status in sessionStorage
                sessionStorage.setItem('isAdminLoggedIn', 'true');
                
                // Redirect to dashboard after 1 second
                setTimeout(() => {
                    window.location.href = 'admin-dashboard.php';
                }, 1000);
            } else {
                // Failed login
                messageDiv.innerHTML = '<div class="alert alert-danger">Invalid username or password</div>';
            }
        });
    </script>
</body>
</html>