<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e0e7ff 0%, #f4f6f9 100%); /* Gradient background */
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow-x: hidden;
}

.container {
    max-width: 700px;
    width: 90%; /* Responsive width */
    margin: 60px auto;
    background: rgba(255, 255, 255, 0.95); /* Glassmorphism effect */
    backdrop-filter: blur(10px); /* Subtle blur for modern look */
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
    transform: translateY(-5px); /* Subtle lift on hover */
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

h2 {
    color: #1e3a8a; /* Deep blue for contrast */
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

ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

li {
    margin: 15px 0;
}

a {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(90deg, #3b82f6, #60a5fa); /* Gradient button */
    color: #ffffff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 1rem;
    transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
}

a:hover {
    background: linear-gradient(90deg, #2563eb, #3b82f6); /* Darker gradient on hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

a:active {
    transform: translateY(0); /* Reset transform on click */
}

a:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3); /* Accessible focus state */
}

a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    ); /* Shine effect */
    transition: 0.5s;
}

a:hover::before {
    left: 100%; /* Shine animation on hover */
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 30px;
        margin: 40px auto;
    }

    h2 {
        font-size: 1.6rem;
    }

    a {
        padding: 10px 20px;
        font-size: 0.95rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 20px;
        margin: 20px auto;
    }

    h2 {
        font-size: 1.4rem;
    }

    a {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
}
</style>
</head>
<body>

<div class="container">
    <h2>Welcome <?= htmlspecialchars($_SESSION['student_name']) ?></h2>
    <ul>
        <li><a href="view_attendance.php">View Attendance</a></li>
        <li><a href="percentage.php">View Attendance Precentage</a></li>
   
        <li><a href="logout.php">Logout</a></li>
        

    </ul>
</div>

</body>
</html>


