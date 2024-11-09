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

    if ($currentPoints > 100) {
        $newPoints = $currentPoints - 100;
        $newLevelNumber = $levelNumber + 1;

        $updateQuery = "UPDATE users SET points = ?, level_number = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("iis", $newPoints, $newLevelNumber, $username);
        $updateStmt->execute();
        $updateStmt->close();

        $row['points'] = $newPoints;
        $row['level_number'] = $newLevelNumber;
    }
} else {
    $row = ['level' => 0, 'points' => 0, 'level_number' => 0];
}

$stmt->close();

// Process search and category filter input
$searchTerm = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Construct the query with an optional category filter
$courseQuery = "SELECT topic, category, description FROM course_outline 
                WHERE (topic LIKE ? OR category LIKE ?)";
if ($categoryFilter && $categoryFilter !== 'all') {
    $courseQuery .= " AND category = ?";
}

$courseStmt = $conn->prepare($courseQuery);
if ($categoryFilter && $categoryFilter !== 'all') {
    $courseStmt->bind_param("sss", $searchTerm, $searchTerm, $categoryFilter);
} else {
    $courseStmt->bind_param("ss", $searchTerm, $searchTerm);
}
$courseStmt->execute();
$courseResult = $courseStmt->get_result();

// Group topics by category
$topicsByCategory = [];
while ($courseRow = $courseResult->fetch_assoc()) {
    $category = $courseRow['category'];
    $topicsByCategory[$category][] = $courseRow;
}

// Sort categories alphabetically (if you want a custom order, adjust this part)
ksort($topicsByCategory);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Course Outline</title>
</head>
<body>
    <header style="width: 100%;">
        <div class="top-bar">
            <div class="user-info">
                <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
                <div class="level-exp">
                    <div class="username"><?php echo strtoupper($_SESSION['username']); ?></div>
                    <span class="level">Level: <?php echo $row['level']?>  <?php echo $row['level_number']?></span>
                    <span class="exp">Experience Points: <?php echo $row['points']?></span>
                </div>
            </div>
            <div class="button-group" style="margin-left: auto;">
                <button class="btn" onclick="window.location.href='user_homepage.php'">Homepage</button>
                <button class="btn" style="background-color: #888;" onclick="window.location.href='user_course_outline.php'">Course Outline</button>
                <button class="btn" onclick="window.location.href='user_quiz_challenges.php'">Quiz Challenges</button>
                <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </header>

    <main>
        <section class="course-outline-document">
            <h2>Course Outline</h2>

            <!-- Search and Category Filter Form -->
            <form method="GET" action="user_course_outline.php" class="search-form">
                <input type="text" name="search" placeholder="Search topics or categories" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <select name="category">
                    <option value="all" <?php echo $categoryFilter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <option value="Category 1" <?php echo $categoryFilter === 'Category 1' ? 'selected' : ''; ?>>Category 1</option>
                    <option value="Category 2" <?php echo $categoryFilter === 'Category 2' ? 'selected' : ''; ?>>Category 2</option>
                    <option value="Category 3" <?php echo $categoryFilter === 'Category 3' ? 'selected' : ''; ?>>Category 3</option>
                    <option value="Category 4" <?php echo $categoryFilter === 'Category 4' ? 'selected' : ''; ?>>Category 4</option>
                </select> 
                <button class="info-btn" type="submit">Search</button>
            </form>
            <br>

            <button class="info-btn" onclick="window.location.href='user_course_outline.php'">Refresh</button>

            <?php if (count($topicsByCategory) > 0): ?>
                <?php foreach ($topicsByCategory as $category => $topics): ?>
                    <div class="category-section">
                        <h3><?php echo htmlspecialchars($category); ?></h3>
                        <?php foreach ($topics as $courseRow): ?>
                            <div class="course-section">
                                <h4><?php echo htmlspecialchars($courseRow['topic']); ?></h4>
                                <p><strong>Category: <b><?php echo htmlspecialchars($courseRow['category']); ?></b></strong></p>
                                <p><strong>Discussion:</strong> <?php echo htmlspecialchars($courseRow['description']); ?></p>
                            </div>
                            <hr> <!-- Divider between course topics -->
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No results found for "<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" in category "<?php echo htmlspecialchars($categoryFilter ?? 'All Categories'); ?>"</p>
            <?php endif; ?>
        </section>
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

