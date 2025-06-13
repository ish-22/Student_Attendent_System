<?php
include "../config.php";
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f4f6f9 100%);
            margin: 0;
            padding: 40px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            max-width: 900px;
            width: 100%;
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.8rem;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #d1d5db;
        }

        th {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            color: #ffffff;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tr:hover {
            background-color: #f3f4f6;
        }

        .actions a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #ffffff;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-right: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .actions a.view-percentage {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .actions a.view-percentage:hover {
            background: linear-gradient(90deg, #059669, #10b981);
            transform: translateY(-2px);
        }

        .actions a.delete {
            background: linear-gradient(90deg, #dc2626, #ef4444);
        }

        .actions a.delete:hover {
            background: linear-gradient(90deg, #b91c1c, #dc2626);
            transform: translateY(-2px);
        }

        .actions a:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            th, td {
                padding: 8px;
                font-size: 0.85rem;
            }

            .actions a {
                padding: 6px 12px;
                font-size: 0.8rem;
                margin-right: 5px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px;
            }

            table {
                font-size: 0.8rem;
            }

            th, td {
                padding: 6px;
            }

            .actions a {
                display: block;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Registered Students</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact No.</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['contact_number']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td class="actions">
                <a href="percentage.php?id=<?= htmlspecialchars($row['id']) ?>" class="view-percentage">View Percentage</a>
                <a href="delete_student.php?id=<?= htmlspecialchars($row['id']) ?>" class="delete" onclick="return confirm('Are you sure to delete this student?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
     <a href="dashboard.php">Back to Dashboard</a>
</div>
  
       
    </div>
</body>
</html>