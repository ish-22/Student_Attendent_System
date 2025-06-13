<?php
session_start();
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact_number']);
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));

    // Check for duplicate username or roll number
    $sql = "SELECT * FROM students WHERE username='$username' OR contact_number='$contact'";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $error = "Username or Contact Number already exists!";
    } else {
        // Insert new student into database
        $sql = "INSERT INTO students (name, contact_number, username, password) VALUES ('$name', '$contact', '$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: index.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e0e7ff 0%, #f4f6f9 100%); /* Consistent gradient */
    margin: 0;
    padding: 20px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: auto;
}

.register-box {
    width: 350px;
    max-width: 90%; /* Responsive width */
    background: rgba(255, 255, 255, 0.95); /* Glassmorphism effect */
    backdrop-filter: blur(10px);
    padding: 30px 40px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeIn 0.5s ease-out; /* Fade-in animation */
}

.register-box:hover {
    transform: translateY(-5px); /* Subtle lift on hover */
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

h2 {
    text-align: center;
    color: #1e3a8a; /* Deep blue consistent with project */
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
    background: linear-gradient(90deg, #3b82f6, #60a5fa); /* Gradient underline */
    margin: 10px auto;
    border-radius: 2px;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 14px;
    margin-bottom: 15px;
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
    background: linear-gradient(90deg, #3b82f6, #60a5fa); /* Gradient button */
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

button:hover {
    background: linear-gradient(90deg, #2563eb, #3b82f6); /* Darker gradient on hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

button:active {
    transform: translateY(0);
}

button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3); /* Accessible focus state */
}

.error {
    color: #dc2626; /* Modern red consistent with project */
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
    font-weight: 500;
}

.success {
    color: #10b981; /* Modern green consistent with project */
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
    font-weight: 500;
}

.login-link {
    text-align: center;
    margin-top: 20px;
    font-size: 0.95rem;
}

.login-link a {
    color: #3b82f6; /* Gradient start color */
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease, text-decoration 0.3s ease;
}

.login-link a:hover {
    color: #2563eb; /* Darker blue on hover */
    text-decoration: underline;
}

.login-link a:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3); /* Accessible focus state */
}

/* Responsive Design */
@media (max-width: 480px) {
    .register-box {
        padding: 20px;
        width: 90%;
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

    .login-link {
        font-size: 0.9rem;
    }
}

/* Animation Keyframes */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="register-box">
    <h2>Student Registration</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <?php 
    if (isset($error)) {
        echo "<p class='error'>$error</p>";
    }
    if (isset($_SESSION['success'])) {
        echo "<p class='success'>{$_SESSION['success']}</p>";
        unset($_SESSION['success']);
    }
    ?>
    <div class="login-link">
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>
</div>

</body>
</html>