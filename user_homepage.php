<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Fetch user details
$query = "SELECT level, points, level_number, role FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentPoints = $row['points'];
    $levelNumber = $row['level_number'];
    $userRole = $row['role']; // Fetch user role

    // Check if points exceed 100
    if ($currentPoints > 100) {
        // Deduct 100 points and increment level number
        $newPoints = $currentPoints - 100;
        $newLevelNumber = $levelNumber + 1;

        // Update the user in the database
        $updateQuery = "UPDATE users SET points = ?, level_number = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("iis", $newPoints, $newLevelNumber, $username);
        $updateStmt->execute();
        $updateStmt->close();

        // Update the local variables to reflect changes
        $row['points'] = $newPoints;
        $row['level_number'] = $newLevelNumber;
    }
} else {
    $row = ['level' => 0, 'points' => 0, 'level_number' => 0, 'role' => 'user'];
}

// Fetch top users based on points only if the user is not an admin
$topUsers = [];
if ($userRole !== 'admin') {
    // Fetch only non-admin users for the top users list
    $topUsersQuery = "SELECT username, level, points, level_number FROM users WHERE role = 'user' ORDER BY points DESC LIMIT 5";
    $topUsersStmt = $conn->prepare($topUsersQuery);
    $topUsersStmt->execute();
    $topUsersResult = $topUsersStmt->get_result();

    while ($user = $topUsersResult->fetch_assoc()) {
        $topUsers[] = $user;
    }

    $topUsersStmt->close(); // Close the statement
}

$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Homepage</title>
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="user-info">
                <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
                <div class="level-exp">
                <div class="username"><?php echo strtoupper($_SESSION['username']); ?></div>
                    <span class="level">Level: <?php echo $row['level']?>  <?php echo $row['level_number']?></span>
                    <span class="exp">Experience Points: <?php echo $row['points']?></span>
                </div>
            </div>
            <div class="button-group" style="margin-left: auto;"> <!-- Align buttons to the right -->
            <button class="btn" style="background-color: #888;">Homepage</button>
            <button class="btn" onclick="window.location.href='user_course_outline.php'">Course Outline</button>
                <button class="btn" onclick="window.location.href='user_quiz_challenges.php'">Quiz Challenges</button>
                <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </header>

    <main>
    <?php if ($userRole !== 'admin'): // Display only for non-admin users ?>
        <div class="top-users">
            <h2>Leaderboards</h2>
            <div id="leaderboard">
            <table>
                <thead>
                    <tr>
                        <th>Players</th>
                        <th>Level</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($topUsers)): ?>
                        <?php foreach ($topUsers as $user):?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['level'].' '); echo htmlspecialchars($user['level_number']); ?></td>
                                <td><?php echo htmlspecialchars($user['points']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <div class="information">
        <p>Welcome to Homepage. Here you can view your information and access various features.</p>
    </div>
</main>

<style>
    /* General Styles */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .top-users {
        max-width: 800px;
        margin: 20px auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
    }

    /* Table title */
    .table-title {
        font-size: 20px;
        color: #4CAF50;  /* Green color for emphasis */
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
        border-bottom: 2px solid #4CAF50; /* Underline the title */
        padding-bottom: 10px;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        padding: 12px;
        text-align: left;
        font-size: 16px;
    }

    th {
        background-color: #4CAF50;
        color: white;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    td {
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
    }

    td:nth-child(even) {
        background-color: #f1f1f1; /* Alternating row colors */
    }

    /* Hover effect on rows */
    tr:hover td {
        background-color: #f1f9f4;
    }

    /* Empty state styling */
    td[colspan="3"] {
        text-align: center;
        font-style: italic;
        color: #999;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        table {
            font-size: 14px;
        }

        th, td {
            padding: 10px;
        }
    }
</style>
    <footer>
        <div class="bottom-bar">
            <div class="contact-info">
                <div class="email">Email: contact@example.com</div>
                <div class="address">Street Address: 123 Example St, City, Country</div>
            </div>
        </div>
    </footer>
</body>
</html>
