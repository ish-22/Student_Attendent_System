<?php
include "../config.php";
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$sql = "SELECT date, status FROM attendance WHERE student_id='$student_id' ORDER BY date DESC";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendance</title>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e0e7ff 0%, #f4f6f9 100%); /* Gradient background */
    margin: 0;
    padding: 20px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow-x: hidden;
}

.container {
    max-width: 800px;
    width: 90%; /* Responsive width */
    margin: 50px auto;
    background: rgba(255, 255, 255, 0.95); /* Glassmorphism effect */
    backdrop-filter: blur(10px); /* Subtle blur */
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
    color: #1e3a8a; /* Deep blue for contrast */
    margin-bottom: 25px;
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

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden; /* Ensure rounded corners */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 14px;
    text-align: center;
    border: 1px solid #d1d5db; /* Softer border color */
    transition: background 0.2s ease;
}

th {
    background: linear-gradient(90deg, #3b82f6, #60a5fa); /* Gradient header */
    color: #ffffff;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    background: #ffffff;
}

tr:nth-child(even) td {
    background: #f9fafb; /* Subtle even-row color */
}

tr:hover td {
    background: #eff6ff; /* Hover effect for rows */
}

.status-present {
    color: #10b981; /* Modern green */
    font-weight: 500;
    text-transform: capitalize;
}

.status-absent {
    color: #dc2626; /* Modern red */
    font-weight: 500;
    text-transform: capitalize;
}

table:focus-within {
    outline: 2px solid #3b82f6; /* Accessible focus state */
    outline-offset: 2px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 20px;
        margin: 40px auto;
    }

    h2 {
        font-size: 1.6rem;
    }

    th, td {
        padding: 10px;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 15px;
        margin: 20px auto;
    }

    h2 {
        font-size: 1.4rem;
    }

    table {
        font-size: 0.85rem;
    }

    th, td {
        padding: 8px;
    }

    /* Stack table for small screens */
    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}
</style>
</head>
<body>

<div class="container">
    <h2>Your Attendance</h2>
    <table>
        <tr><th>Date</th><th>Status</th></tr>
        <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td class="<?= $row['status'] == 'Present' ? 'status-present' : 'status-absent' ?>">
                <?= htmlspecialchars($row['status']) ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
     <a href="dashboard.php" class="back-link">Back to Dashboard</a>
</div>

</body>
</html>
