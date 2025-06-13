<?php
session_start();

// Manual admin credentials
$correct_username = "admin@gmail.com";
$correct_password = "admin"; // plaintext (for demonstration only)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user === $correct_username && $pass === $correct_password) {
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid login!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #d1e8ff 0%, #e6f0fa 100%); /* Admin-specific gradient */
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px;
    overflow: auto;
}

.login-box {
    background: rgba(255, 255, 255, 0.95); /* Glassmorphism effect */
    backdrop-filter: blur(10px);
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    width: 100%;
    max-width: 400px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-box:hover {
    transform: translateY(-5px); /* Subtle lift on hover */
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

h2 {
    text-align: center;
    color: #1e40af; /* Darker blue for admin branding */
    margin-bottom: 30px;
    font-weight: 600;
    font-size: 1.8rem;
    letter-spacing: 0.5px;
    position: relative;
}

h2::after {
    content: '';
    display: block;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #2563eb, #4f46e5); /* Admin gradient underline */
    margin: 10px auto;
    border-radius: 2px;
}

label {
    display: block;
    margin-top: 15px;
    font-weight: 500;
    color: #1e40af;
    font-size: 0.95rem;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 14px;
    margin-top: 5px;
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
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

button {
    width: 100%;
    background: linear-gradient(90deg, #2563eb, #4f46e5); /* Admin gradient button */
    color: #ffffff;
    padding: 14px;
    margin-top: 20px;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

button:hover {
    background: linear-gradient(90deg, #1e40af, #2563eb);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

button:active {
    transform: translateY(0);
}

button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
}

.error-message {
    color: #dc2626;
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 480px) {
    .login-box {
        padding: 20px;
        max-width: 90%;
    }

    h2 {
        font-size: 1.5rem;
    }

    label {
        font-size: 0.9rem;
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

<div class="login-box">
    <h2>Admin Login</h2>
    <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
</div>

</body>
</html>
