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

// Initialize search term
$search_term = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $search_term = trim($_POST['search'] ?? '');
}

// Prepare SQL query with optional search filter
$sql = "SELECT s.name, s.contact_number, a.date, a.status
        FROM attendance a
        JOIN students s ON a.student_id = s.id";
$params = [];
$types = '';
if ($search_term) {
    $sql .= " WHERE s.name LIKE ?";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $types .= 's';
}
$sql .= " ORDER BY a.date DESC";

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
    <title>Attendance Records</title>
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

.container {
    max-width: 900px;
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

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    animation: fadeIn 0.5s ease-out; /* Fade-in animation */
}

th, td {
    padding: 14px;
    text-align: center;
    border: 1px solid #d1d5db; /* Softer border */
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
    background: #eff6ff; /* Hover effect */
}

td.status-Present {
    color: #10b981; /* Modern green consistent with project */
    font-weight: 500;
    text-transform: capitalize;
}

td.status-Absent {
    color: #dc2626; /* Modern red consistent with project */
    font-weight: 500;
    text-transform: capitalize;
}

table:focus-within {
    outline: 2px solid #3b82f6; /* Accessible focus state */
    outline-offset: 2px;
}

/* Search Container Styles (view_all_attendance.php) */
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
    font-variation-settings: 'wght' 400;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-blue-light);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
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
@media (max-width: 480px) {
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

    th, td {
        padding: 10px;
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

    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
        font-size: 0.85rem;
    }

    th, td {
        padding: 8px;
    }
}

/* Animation Keyframes */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="container">
    <h2>Attendance Records</h2>
    <div class="search-container">
        <form method="post" autocomplete="off" aria-label="Search attendance records">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="text" id="search-input" name="search" class="search-input" placeholder="Search by student name..." value="<?= htmlspecialchars($search_term) ?>" aria-label="Search by name">
            <button type="submit" aria-label="Search">Search</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table role="table" aria-label="Attendance Records">
            <thead>
                <tr role="row">
                    <th role="columnheader">Name</th>
                    <th role="columnheader">Contact Number</th>
                    <th role="columnheader">Date</th>
                    <th role="columnheader">Status</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr role="row" class="attendance-row" data-name="<?= htmlspecialchars(strtolower($row['name'])) ?>">
                    <td role="cell"><?= htmlspecialchars($row['name']) ?></td>
                    <td role="cell"><?= htmlspecialchars($row['contact_number']) ?></td>
                    <td role="cell"><?= htmlspecialchars($row['date']) ?></td>
                    <td role="cell" class="status-<?= htmlspecialchars($row['status']) ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
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

 