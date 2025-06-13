<?php
session_start();
require_once '../config.php';

// Ensure timezone consistency
date_default_timezone_set('Asia/Kolkata');

$error = '';
$success = '';
$show_reset_form = false;
$token = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && !isset($_POST['new_password'])) {
        // Step 1: Verify username
        $username = trim($_POST['username']);
        $stmt_select = $conn->prepare("SELECT id FROM students WHERE username = ?");
        if ($stmt_select === false) {
            error_log("Username check prepare failed: " . $conn->error);
            $error = "Database error.";
        } else {
            $stmt_select->bind_param("s", $username);
            $stmt_select->execute();
            $result = $stmt_select->get_result();

            if ($result->num_rows > 0) {
                // Generate token and store in password_resets
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                error_log("Attempting to insert token for $username: token=$token, expires_at=$expires_at");
                $stmt_insert = $conn->prepare("INSERT INTO password_resets (username, token, expires_at) VALUES (?, ?, ?)");
                if ($stmt_insert === false) {
                    error_log("Insert prepare failed: " . $conn->error);
                    $error = "Database error.";
                } else {
                    $stmt_insert->bind_param("sss", $username, $token, $expires_at);
                    if ($stmt_insert->execute()) {
                        $show_reset_form = true;
                        $_SESSION['reset_token'] = $token;
                        $_SESSION['reset_username'] = $username;
                        error_log("Token inserted successfully for $username");
                    } else {
                        error_log("Insert failed: " . $conn->error);
                        $error = "Error generating reset token.";
                    }
                    $stmt_insert->close();
                }
            } else {
                $error = "Username not found.";
            }
            $stmt_select->close();
        }
    } elseif (isset($_POST['new_password']) && isset($_SESSION['reset_token']) && isset($_SESSION['reset_username'])) {
        // Step 2: Reset password
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);
        $token = $_SESSION['reset_token'];
        $username = $_SESSION['reset_username'];

        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 4) {
                // Verify token
                error_log("Verifying token: $token for username: $username");
                $stmt_verify = $conn->prepare("SELECT username FROM password_resets WHERE token = ? AND username = ? AND expires_at > NOW()");
                if ($stmt_verify === false) {
                    error_log("Token verify prepare failed: " . $conn->error);
                    $error = "Database error.";
                } else {
                    $stmt_verify->bind_param("ss", $token, $username);
                    $stmt_verify->execute();
                    $result = $stmt_verify->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $hashed_password = md5($new_password); // TODO: Use password_hash()
                        $stmt_update = $conn->prepare("UPDATE students SET password = ? WHERE username = ?");
                        if ($stmt_update === false) {
                            error_log("Password update prepare failed: " . $conn->error);
                            $error = "Database error.";
                        } else {
                            $stmt_update->bind_param("ss", $hashed_password, $username);
                            if ($stmt_update->execute()) {
                                // Delete used token
                                $stmt_delete = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                                if ($stmt_delete) {
                                    $stmt_delete->bind_param("s", $token);
                                    $stmt_delete->execute();
                                    $stmt_delete->close();
                                }
                                $success = "Password reset successfully. <a href='index.php'>Login</a>";
                                unset($_SESSION['reset_token']);
                                unset($_SESSION['reset_username']);
                                error_log("Password reset successful for $username");
                            } else {
                                error_log("Password update failed: " . $conn->error);
                                $error = "Error updating password.";
                            }
                            $stmt_update->close();
                        }
                    } else {
                        error_log("Token verification failed for $username: token=$token");
                        $error = "Invalid or expired token.";
                        unset($_SESSION['reset_token']);
                        unset($_SESSION['reset_username']);
                    }
                    $stmt_verify->close();
                }
            } else {
                $error = "Password must be at least 4 characters.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $error = "Invalid request. Please start over.";
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_username']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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

        .reset-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .reset-box:hover {
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

        button {
            width: 100%;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 500;
            border: none;
            border-radius: 10px;
            color: #ffffff;
            cursor: pointer;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: #dc2626;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .success {
            color: #10b981;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .reset-box {
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 1.5rem;
            }

            input[type="text"],
            input[type="password"],
            button {
                padding: 12px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="reset-box">
    <h2>Forgot Password</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>

    <?php if (!$show_reset_form && !$success): ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <button type="submit">Verify Username</button>
        </form>
    <?php elseif ($show_reset_form): ?>
        <form method="POST" action="">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
    <div class="back-link">
        <a href="index.php">Back to Login</a>
    </div>
</div>
</body>
</html>