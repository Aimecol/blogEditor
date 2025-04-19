<?php
require_once 'php/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $action = $_POST['action'] ?? 'login';
    
    if ($action === 'register') {
        if (register($username, $password)) {
            $success = 'Registration successful! Please login.';
        } else {
            $error = 'Registration failed. Username might be taken.';
        }
    } else {
        if (login($username, $password)) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Editor - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --dark-color: #2c3e50;
            --light-color: #f5f6fa;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 2rem;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            background: var(--primary-color);
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #357abd;
        }

        .toggle-form {
            text-align: center;
            margin-top: 1rem;
        }

        .toggle-form button {
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
        }

        .error {
            color: var(--error-color);
            text-align: center;
            margin-bottom: 1rem;
        }

        .success {
            color: var(--success-color);
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-pen-fancy"></i> Blog Editor</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form id="authForm" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <input type="hidden" name="action" value="login" id="action">
            <button type="submit" class="btn" id="submitBtn">Login</button>
        </form>

        <div class="toggle-form">
            <button type="button" onclick="toggleForm()" id="toggleBtn">
                Don't have an account? Register
            </button>
        </div>
    </div>

    <script>
        function toggleForm() {
            const action = document.getElementById('action');
            const submitBtn = document.getElementById('submitBtn');
            const toggleBtn = document.getElementById('toggleBtn');
            
            if (action.value === 'login') {
                action.value = 'register';
                submitBtn.textContent = 'Register';
                toggleBtn.textContent = 'Already have an account? Login';
            } else {
                action.value = 'login';
                submitBtn.textContent = 'Login';
                toggleBtn.textContent = 'Don\'t have an account? Register';
            }
        }
    </script>
</body>
</html>