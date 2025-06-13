<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
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
    overflow-x: hidden;
}

.dashboard {
    max-width: 500px;
    width: 90%; /* Responsive width */
    margin: 40px auto;
    background: rgba(255, 255, 255, 0.95); /* Glassmorphism effect */
    backdrop-filter: blur(10px);
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard:hover {
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

ul {
    list-style: none;
    padding: 0;
    margin-top: 20px;
}

li {
    margin: 15px 0;
}

a {
    display: block;
    text-decoration: none;
    background: linear-gradient(90deg, #3b82f6, #60a5fa); /* Gradient button */
    color: #ffffff;
    padding: 14px;
    text-align: center;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 500;
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
    transform: translateY(0);
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
    transition: left 0.5s ease;
}

a:hover::before {
    left: 100%; /* Shine animation on hover */
}

/* Optional Admin Sidebar for Enhanced Dashboard */
.admin-container {
    display: flex;
    min-height: 100vh;
    width: 100%;
    background: #f4f6f9;
}

.sidebar {
    width: 250px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 20px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    position: fixed;
    height: 100%;
    overflow-y: auto;
    transition: transform 0.3s ease;
}

.sidebar h3 {
    color: #1e3a8a;
    font-size: 1.5rem;
    margin-bottom: 20px;
    text-align: center;
}

.sidebar a {
    background: transparent;
    color: #1e3a8a;
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 400;
}

.sidebar a:hover {
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    color: #ffffff;
    transform: none;
}

.sidebar a.active {
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    color: #ffffff;
}

.content {
    margin-left: 270px; /* Account for sidebar width */
    padding: 20px;
    width: calc(100% - 270px);
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    margin: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard {
        padding: 20px;
        margin: 30px auto;
    }

    h2 {
        font-size: 1.6rem;
    }

    a {
        padding: 12px;
        font-size: 0.95rem;
    }

    .admin-container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: static;
        transform: none;
    }

    .content {
        margin-left: 0;
        width: 90%;
        margin: 20px auto;
    }
}

@media (max-width: 480px) {
    .dashboard {
        padding: 15px;
        margin: 20px auto;
    }

    h2 {
        font-size: 1.4rem;
    }

    a {
        padding: 10px;
        font-size: 0.9rem;
    }
}
</style>
</head>
<body>

<div class="dashboard">
    <h2>Admin Dashboard</h2>
    <ul>
        <li><a href="add_student.php">Add Student</a></li>
        <li><a href="mark_attendance.php">Mark Attendance</a></li>
        <li><a href="view_attendance.php">View Attendance</a></li>
         <li><a href="view_students.php">View All Students</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

</body>
</html>
