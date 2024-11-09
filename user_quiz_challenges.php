<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$query = "SELECT level, points, level_number FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentPoints = $row['points'];
    $levelNumber = $row['level_number'];

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
    $row = ['level' => 0, 'points' => 0, 'level_number' => 0];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
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
                <button class="btn" onclick="window.location.href='user_homepage.php'">Homepage</button>
                <button class="btn" onclick="window.location.href='user_course_outline.php'">Course Outline</button>
                <button class="btn" style="background-color: #888;">Quiz Challenges</button>
                <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </header>

    <main>
        <div class="information">
            <p>Welcome to the dashboard. Here you can view your information and access various features.</p>
            <div class="info-box-container">
                <?php
                // Array of boxes for easier management
                $boxes = [
                    ['title' => 'Challenge', 'information'=>'Test out your skills upon tackling the questions provided from 3 diffrent difficulties', 'image' => 'images/challenge_icon.png', 'link' => 'challenge.php'],
                    ['title' => 'Easy', 'information'=>'Take Easy Level Questions to gain minor amount of experience points', 'image' => 'images/easy_icon.png', 'link' => 'easy.php'],
                    ['title' => 'Moderate', 'information'=>'Take Moderate Level Questions to gain a considerable amount of experience points', 'image' => 'images/moderate_icon.png', 'link' => 'moderate.php'],
                    ['title' => 'Hard', 'information'=>'Take Hard Level Questions to gain Huge amount of experience points', 'image' => 'images/hard_icon.png', 'link' => 'hard.php']
                ];

                foreach ($boxes as $box) {
                    echo '<div class="info-box">';
                    echo '<h3>' . htmlspecialchars($box['title']) . '</h3>';
                    echo '<img src="' . htmlspecialchars($box['image']) . '" alt="' . htmlspecialchars($box['title']) . '" class="info-img">';
                    echo '<p>' . htmlspecialchars($box['information']) . '.</p>';
                    echo '<button onclick="window.location.href=\'' . htmlspecialchars($box['link']) . '\'" class="info-btn">TEST</button>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>
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
