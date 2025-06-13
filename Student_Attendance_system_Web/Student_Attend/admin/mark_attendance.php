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

$message = '';
$search_term = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (isset($_POST['date'], $_POST['status'])) {
        $date = trim($_POST['date']);
        // Validate date format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $message = "Invalid date format.";
        } else {
            $stmt_check = $conn->prepare("SELECT id FROM attendance WHERE date = ? AND student_id = ?");
            if ($stmt_check === false) {
                error_log("Prepare failed: " . $conn->error);
                $message = "Database error.";
            } else {
                $stmt_insert = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
                if ($stmt_insert === false) {
                    error_log("Prepare failed: " . $conn->error);
                    $message = "Database error.";
                } else {
                    $success = true;
                    foreach ($_POST['status'] as $sid => $status) {
                        // Validate student_id and status
                        $sid = (int)$sid;
                        if (!in_array($status, ['Present', 'Absent'])) {
                            $message = "Invalid status value.";
                            $success = false;
                            break;
                        }
                        // Check for existing attendance
                        $stmt_check->bind_param("si", $date, $sid);
                        $stmt_check->execute();
                        $result_check = $stmt_check->get_result();
                        if ($result_check->num_rows == 0) {
                            // Insert new attendance
                            $stmt_insert->bind_param("iss", $sid, $date, $status);
                            if (!$stmt_insert->execute()) {
                                error_log("Execute failed: " . $stmt_insert->error);
                                $message = "Error marking attendance.";
                                $success = false;
                                break;
                            }
                        }
                    }
                    if ($success && $message === '') {
                        $message = "Attendance marked successfully!";
                    }
                    $stmt_insert->close();
                }
                $stmt_check->close();
            }
        }
    }
    // Capture search term if provided
    $search_term = trim($_POST['search'] ?? '');
}

// Prepare SQL query for students with optional search filter
$sql = "SELECT id, name FROM students";
$params = [];
$types = '';
if ($search_term) {
    $sql .= " WHERE name LIKE ?";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $types .= 's';
}
$sql .= " ORDER BY name ASC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    error_log("Prepare failed: " . $conn->error);
    die("Database error.");
}
if ($search_term) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
 <style>@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

/* Custom Properties */
:root {
    --primary-blue: #1e3a8a;
    --primary-blue-light: #3b82f6;
    --primary-blue-lighter: #60a5fa;
    --success-green: #10b981;
    --border-color: #d1d5db;
    --transition: all 0.3s ease;
    --border-radius: 16px;
    --border-radius-sm: 10px;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.2);
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e0e7ff 0%, #f4f6f9 100%);
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
    width: 90%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    margin: 40px auto;
    padding: 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

h2 {
    text-align: center;
    color: var(--primary-blue);
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
    background: linear-gradient(90deg, var(--primary-blue-light), var(--primary-blue-lighter));
    margin: 10px auto;
    border-radius: 2px;
}

label {
    display: block;
    margin-top: 15px;
    font-weight: 500;
    color: var(--primary-blue);
    font-size: 0.95rem;
}

input[type="date"] {
    width: 100%;
    padding: 14px;
    margin-top: 5px;
    margin-bottom: 20px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    box-sizing: border-box;
    font-size: 1rem;
    background: #f9fafb;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input[type="date"]:focus {
    outline: none;
    border-color: var(--primary-blue-light);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

select {
    width: 100%;
    padding: 14px;
    margin-top: 5px;
    margin-bottom: 20px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    box-sizing: border-box;
    font-size: 1rem;
    background: #f9fafb;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

select:focus {
    outline: none;
    border-color: var(--primary-blue-light);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

table th, table td {
    padding: 14px;
    text-align: left;
    border: 1px solid var(--border-color);
    transition: background 0.2s ease;
}

table th {
    background: linear-gradient(90deg, var(--primary-blue-light), var(--primary-blue-lighter));
    color: #ffffff;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

table td {
    background: #ffffff;
}

table tr:nth-child(even) td {
    background: #f9fafb;
}

table tr:hover td {
    background: #eff6ff;
}

button {
    width: 100%; /* Fixed for Submit Attendance */
    background: linear-gradient(90deg, var(--primary-blue-light), var(--primary-blue-lighter));
    color: #ffffff;
    border: none;
    padding: 14px;
    font-size: 1.1rem; /* Improved readability */
    font-weight: 500;
    border-radius: var(--border-radius-sm);
    cursor: pointer;
    margin-top: 20px;
    transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

button:hover {
    background: linear-gradient(90deg, #2563eb, var(--primary-blue-light));
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

button:active {
    transform: translateY(0);
}

button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.message {
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--success-green);
}

/* Search Container Styles (mark_attendance.php) */
.search-container {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.search-container form {
    display: flex;
    width: 100%;
    gap: 0.5rem;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    font-size: 0.95rem;
    background: #f9fafb;
    transition: var(--transition);
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-blue-light);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-container button {
    width: auto;
    padding: 0.75rem 1.5rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 20px;
        margin: 30px auto;
    }

    h2 {
        font-size: 1.6rem;
    }

    input[type="date"],
    select,
    button {
        padding: 12px;
        font-size: 1rem;
    }

    table th, table td {
        padding: 10px;
        font-size: 0.9rem;
    }

    .search-container {
        gap: 0.75rem;
    }

    .search-input {
        padding: 0.75rem;
        font-size: 0.9rem;
    }

    .search-container button {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 15px;
        max-width: 90%;
    }

    h2 {
        font-size: 1.4rem;
    }

    label {
        font-size: 0.9rem;
    }

    input[type="date"],
    select,
    button {
        padding: 10px;
        font-size: 0.95rem;
    }

    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    table th, table td {
        padding: 8px;
        font-size: 0.85rem;
    }

    .search-container {
        flex-direction: column;
        gap: 0.75rem;
    }

    .search-container form {
        flex-direction: column;
        gap: 0.5rem;
    }

    .search-input {
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
    }

    .search-container button {
        width: 100%;
        padding: 0.625rem;
        font-size: 0.9rem;
    }
}</style>
</head>
<body>
<div class="container">
    <h2>Mark Attendance</h2>
    <?php if ($message): ?>
        <p class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
    <div class="search-container">
        <form method="post" autocomplete="off" aria-label="Search students">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="text" id="search-input" name="search" class="search-input" placeholder="Search by student name..." value="<?= htmlspecialchars($search_term) ?>" aria-label="Search by name">
            <button type="submit" aria-label="Search">Search</button>
        </form>
    </div>
    <form method="post" autocomplete="off" aria-label="Mark attendance">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>
        <div class="table-wrapper">
            <table role="table" aria-label="Student Attendance">
                <thead>
                    <tr role="row">
                        <th role="columnheader">Name</th>
                        <th role="columnheader">Status</th>
                    </tr>
                </thead>
                <tbody id="attendance-table-body">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr role="row" class="attendance-row" data-name="<?= htmlspecialchars(strtolower($row['name'])) ?>">
                        <td role="cell"><?= htmlspecialchars($row['name']) ?></td>
                        <td role="cell">
                            <select name="status[<?= htmlspecialchars($row['id']) ?>]" aria-label="Attendance status for <?= htmlspecialchars($row['name']) ?>">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <button type="submit">Submit Attendance</button>
    </form>
    <div class="back-link">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</div>
<script>
document.getElementById('search-input').addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.attendance-row');
    rows.forEach(row => {
        const name = row.dataset.name;
        row.style.display = name.includes(searchValue) ? '' : 'none';
    });
});
</script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>


 