<?php
session_start();
include "../config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Check for student ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid student ID.";
    header("Location: view_students.php");
    exit();
}

$student_id = (int)$_GET['id'];

// Fetch student details for confirmation
$stmt = $conn->prepare("SELECT name, contact_number FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $_SESSION['error'] = "Student not found.";
    header("Location: view_students.php");
    exit();
}

$student = $result->fetch_assoc();
$student_name = $student['name'];
$contact_number = $student['contact_number'];
$stmt->close();

// Handle deletion if confirmed
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm'])) {
    // Begin transaction
    $conn->begin_transaction();
    try {
        // Delete attendance records
        $stmt = $conn->prepare("DELETE FROM attendance WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        // Delete student
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $_SESSION['success'] = "Student deleted successfully.";
        header("Location: view_students.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting student: " . $e->getMessage();
        header("Location: view_students.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Student</title>
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
            max-width: 400px;
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

        form {
            margin-bottom: 20px;
        }

        button, .cancel-link {
            display: inline-block;
            padding: 14px 20px;
            font-size: 1.1rem;
            font-weight: 500;
            border: none;
            border-radius: 10px;
            color: #ffffff;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease, transform 0.2s ease;
            margin: 0 10px;
        }

        button {
            background: linear-gradient(90deg, #dc2626, #ef4444);
        }

        button:hover {
            background: linear-gradient(90deg, #b91c1c, #dc2626);
            transform: translateY(-2px);
        }

        .cancel-link {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .cancel-link:hover {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            transform: translateY(-2px);
        }

        button:active, .cancel-link:active {
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

            button, .cancel-link {
                padding: 12px 16px;
                font-size: 1rem;
                display: block;
                margin: 10px auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delete Student</h2>
        <p>Are you sure you want to delete the following student?</p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student_name); ?></p>
        <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($contact_number); ?></p>
        <form method="post">
            <button type="submit" name="confirm">Confirm Delete</button>
            <a href="view_students.php" class="cancel-link">Cancel</a>
        </form>
    </div>
</body>
</html>