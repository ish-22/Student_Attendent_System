<?php
session_start();
require_once "../config.php";

// Redirect if already logged in
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Check "Remember Me" cookie
if (!isset($_SESSION['student_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $conn->prepare("SELECT id, name, username FROM students WHERE remember_token = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
    } else {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $_SESSION['student_id'] = $row['id'];
            $_SESSION['student_name'] = $row['name'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid token, clear cookie
            setcookie('remember_me', '', time() - 3600, "/");
        }
        $stmt->close();
    }
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // TODO: Switch to password_hash()
    $remember = isset($_POST['remember_me']);

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, username, password FROM students WHERE username = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        $error = "Database error.";
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['password'] === $password) {
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                $_SESSION['username'] = $row['username'];

                if ($remember) {
                    // Generate and store remember token
                    $token = bin2hex(random_bytes(32));
                    $update_stmt = $conn->prepare("UPDATE students SET remember_token = ? WHERE id = ?");
                    if ($update_stmt === false) {
                        error_log("Prepare failed for remember_token update: " . $conn->error);
                        $error = "Error setting remember token.";
                    } else {
                        $update_stmt->bind_param("si", $token, $row['id']);
                        if ($update_stmt->execute()) {
                            // Set secure cookie (30 days)
                            setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), "/", "", true, true); // Secure, HttpOnly
                        } else {
                            error_log("Execute failed for remember_token update: " . $conn->error);
                            $error = "Error saving remember token.";
                        }
                        $update_stmt->close();
                    }
                }

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Username not found!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f4f6f9 100%);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: auto;
        }

        .login-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 1.8rem;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 1rem;
            background: #f9fafb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        button, .register-link a, .forgot-link a {
            width: 100%;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 500;
            border: none;
            border-radius: 10px;
            color: #ffffff;
            cursor: pointer;
            text-decoration: none;
            display: block;
            box-sizing: border-box;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        button:hover {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .register-link, .forgot-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .register-link a:hover {
            background: linear-gradient(90deg, #059669, #10b981);
            transform: translateY(-2px);
        }

        .forgot-link a {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .forgot-link a:hover {
            background: linear-gradient(90deg, #dc2626, #ef4444);
            transform: translateY(-2px);
        }

        .error {
            color: #dc2626;
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-container input {
            width: auto;
            margin-right: 10px;
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 1.5rem;
            }

            input[type="text"],
            input[type="password"],
            button,
            .register-link a,
            .forgot-link a {
                padding: 12px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Student Login</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <div class="checkbox-container">
            <input type="checkbox" name="remember_me" id="remember_me">
            <label for="remember_me">Remember Me</label>
        </div>
        <button type="submit">Login</button>
    </form>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <div class="register-link">
        <a href="register.php">Register</a>
    </div>
    <div class="forgot-link">
        <a href="forgot_password.php">Forgot Password?</a>
    </div>
</div>
</body>
</html>