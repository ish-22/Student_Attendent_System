
<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Validate inputs
    if (!isset($_POST['name'], $_POST['contact'], $_POST['username'], $_POST['password'])) {
        $error = "All fields are required.";
    } else {
        $name = trim($_POST['name']);
        $contact = trim($_POST['contact']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Validate contact number (10-digit) and password length
        if (!preg_match('/^[0-9]{10}$/', $contact)) {
            $error = "Contact number must be 10 digits.";
        } elseif (strlen($password) < 4) {
            $error = "Password must be at least 4 characters.";
        } else {
            // Check for duplicate username or contact
            $stmt = $conn->prepare("SELECT id FROM students WHERE username = ? OR contact_number = ?");
            if ($stmt === false) {
                error_log("Prepare failed: " . $conn->error);
                $error = "Database error.";
            } else {
                $stmt->bind_param("ss", $username, $contact);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error = "Username or Contact Number already exists!";
                } else {
                    // Insert new student
                    $hashed_password = md5($password); // TODO: Replace with password_hash()
                    $stmt = $conn->prepare("INSERT INTO students (name, contact_number, username, password) VALUES (?, ?, ?, ?)");
                    if ($stmt === false) {
                        error_log("Prepare failed: " . $conn->error);
                        $error = "Database error.";
                    } else {
                        $stmt->bind_param("ssss", $name, $contact, $username, $hashed_password);
                        if ($stmt->execute()) {
                            $success = "Student added successfully!";
                        } else {
                            error_log("Execute failed: " . $conn->error);
                            $error = "Error adding student: " . $conn->error;
                        }
                    }
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
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

.container {
    max-width: 400px;
    width: 90%; /* Responsive width */
    background: rgba(255, 255, 255, 0.95); /* Glassmorphism effect */
    backdrop-filter: blur(10px);
    margin: 40px auto;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
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

label {
    display: block;
    margin-top: 15px;
    font-weight: 500;
    color: #1e3a8a;
    font-size: 0.95rem;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 14px;
    margin-top: 5px;
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
    background: linear-gradient(90deg, #3b82f6, #60a5fa); /* Gradient button */
    color: #ffffff;
    border: none;
    padding: 14px;
    font-size: 1.1rem;
    font-weight: 500;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 20px;
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

.message {
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
    font-weight: 500;
}

.success {
    color: #10b981; /* Modern green consistent with project */
}

.error {
    color: #dc2626; /* Modern red consistent with project */
}

/* Responsive Design */
@media (max-width: 480px) {
    .container {
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
<div class="container">
    <h2>Add Student</h2>
    <?php if ($success): ?>
        <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="contact">Contact Number:</label>
        <input type="text" id="contact" name="contact" required pattern="[0-9]{10}" title="Enter a 10-digit contact number">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="4">
        <button type="submit">Add Student</button>
    </form>
    <div class="back-link">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</div>
</body>
</html>


