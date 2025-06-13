<?php
session_start();
include "../config.php";

// Check if viewing as admin or student
$is_admin = isset($_SESSION['admin']) && isset($_GET['id']);
if ($is_admin) {
    $student_id = (int)$_GET['id'];
    // Fetch student name for admin view
    $stmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $student_name = $student['name'];
    } else {
        $error = "Student not found.";
    }
    $stmt->close();
} elseif (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $student_name = $_SESSION['student_name'];
} else {
    header("Location: index.php");
    exit();
}

// Query to fetch attendance records
if (!isset($error)) {
    $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM attendance WHERE student_id = ? GROUP BY status");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $present = 0;
    $absent = 0;
    $total = 0;

    while ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'Present') {
            $present = $row['count'];
        } elseif ($row['status'] == 'Absent') {
            $absent = $row['count'];
        }
    }
    $total = $present + $absent;

    $present_percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
    $absent_percentage = $total > 0 ? round(($absent / $total) * 100, 2) : 0;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Percentage</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
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

        .container {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 500px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #1e3a8a;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.8rem;
            letter-spacing: 0.5px;
        }

        p {
            color: #4b5563;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        canvas {
            max-width: 100%;
            margin: 20px auto;
        }

        .error {
            color: #dc2626;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 20px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 14px 20px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .back-link:hover {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            transform: translateY(-2px);
        }

        .back-link:active {
            transform: translateY(0);
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

            canvas {
                max-width: 100%;
            }

            .back-link {
                padding: 12px 16px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Attendance Percentage</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
            <p><?php echo $is_admin ? "Student: " : "Welcome, "; ?><?php echo htmlspecialchars($student_name); ?>!</p>
            <?php if ($total > 0): ?>
                <canvas id="attendanceChart"></canvas>
                <p>Present: <?php echo $present_percentage; ?>% | Absent: <?php echo $absent_percentage; ?>%</p>
            <?php else: ?>
                <p class="error">No attendance records found.</p>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?php echo $is_admin ? 'view_students.php' : 'dashboard.php'; ?>" class="back-link">Back to <?php echo $is_admin ? 'Students List' : 'Dashboard'; ?></a>
    </div>

    <?php if ($total > 0 && !isset($error)): ?>
    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [<?php echo $present_percentage; ?>, <?php echo $absent_percentage; ?>],
                    backgroundColor: ['#10b981', '#dc2626'],
                    borderColor: ['#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                family: 'Poppins'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>