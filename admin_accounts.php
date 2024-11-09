<?php
session_start();
include 'db.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Query to get only users (non-admin) ordered by points and level in descending order
$result = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY points DESC, level_number DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> 
    <title>Admin Accounts</title>
</head>
<body>
<div class="top-bar">
    <div class="user-info">
        <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
        <div class="username"><?php echo $_SESSION['username']; ?></div>
    </div>
    <div class="button-group" style="margin-left: auto;">
        <button class="btn" onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
        <button class="btn" onclick="window.location.href='admin_course_outline_edit.php'">Course</button>
        <button class="btn" style="background-color: #888;" onclick="window.location.href='admin_accounts.php'">Accounts</button>
        <button class="btn" onclick="window.location.href='admin_question_pool.php'">Questions Pool</button>
        <button class="btn" onclick="window.location.href='admin_archive_questions.php'">Archive Questions</button>
        <button class="btn" onclick="window.location.href='admin_question_stat.php'">Questions Statistics</button>
        <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
        </div>
</div>
<div class="body">
    <div class="admin-container">
        <h3>Registered Users:</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Points</th>
                    <th>Level</th>
                    <th>Level Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['points']); ?></td>
                        <td><?php echo htmlspecialchars($row['level']); ?></td>
                        <td><?php echo htmlspecialchars($row['level_number']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
