<?php
session_start();
include 'db.php'; // Ensure your db.php includes the connection details

// Check if the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Get the username from the session
$username = $_SESSION['username'];

// Fetch user level and points
$query = "SELECT level, points FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->num_rows > 0 ? $result->fetch_assoc() : ['level' => 0, 'points' => 0];

// Pagination and Filtering Logic
$limit = 10; // Number of questions per page
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Get the selected difficulty from the filter
$difficultyFilter = $_GET['difficulty'] ?? 'all';

// Get the selected sort option from the filter
$sortOption = $_GET['sort'] ?? 'none';

// Prepare the SQL statement to get questions
$sql = "SELECT 
            q.id AS question_id, 
            q.question,
            q.difficulty,
            COALESCE(SUM(ua.is_correct = 1), 0) AS correct_answers,
            COALESCE(SUM(ua.is_correct = 0), 0) AS incorrect_answers,
            q.archive
        FROM questions q
        LEFT JOIN user_answers ua ON q.id = ua.question_id 
        WHERE 1 = 1"; // Always true for easier dynamic query building

// Add difficulty filter if set
if ($difficultyFilter !== 'all') {
    $sql .= " AND q.difficulty = ?";
}

// Add sorting logic
if ($sortOption === 'correct') {
    $sql .= " GROUP BY q.id, q.question, q.difficulty ORDER BY correct_answers DESC";
} elseif ($sortOption === 'incorrect') {
    $sql .= " GROUP BY q.id, q.question, q.difficulty ORDER BY incorrect_answers DESC";
} else {
    $sql .= " GROUP BY q.id, q.question, q.difficulty";
}

// Add pagination
$sql .= " LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Bind the parameters
if ($difficultyFilter !== 'all') {
    $stmt->bind_param("sii", $difficultyFilter, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC);

// Get the total number of questions to determine the number of pages
$totalQuery = "SELECT COUNT(*) AS total FROM questions WHERE 1 = 1"; // Count based on the same conditions
if ($difficultyFilter !== 'all') {
    $totalQuery .= " AND difficulty = ?";
}
$totalStmt = $conn->prepare($totalQuery);
if ($difficultyFilter !== 'all') {
    $totalStmt->bind_param("s", $difficultyFilter);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalCount = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalCount / $limit);

$totalStmt->close();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Question Statistics</title>
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="user-info">
                <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
                <div class="username"><?php echo strtoupper(htmlspecialchars($username)); ?></div>
            </div>
            <div class="button-group" style="margin-left: auto;">
                <button class="btn" onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
                <button class="btn" onclick="window.location.href='admin_course_outline_edit.php'">Course</button>
                <button class="btn" onclick="window.location.href='admin_accounts.php'">Accounts</button>
                <button class="btn" onclick="window.location.href='admin_question_pool.php'">Questions Pool</button>
                <button class="btn" onclick="window.location.href='admin_archive_questions.php'">Archive Questions</button>
                <button class="btn" style="background-color: #888;" onclick="window.location.href='admin_question_stat.php'">Questions Statistics</button>
                <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </header>

    <main>
        <div class="information">
            <h1>Question Statistics</h1>
            <form method="GET">
                <label for="difficulty">Select Difficulty:</label>
                <select name="difficulty" id="difficulty">
                    <option value="all" <?php echo $difficultyFilter == 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="easy" <?php echo $difficultyFilter == 'easy' ? 'selected' : ''; ?>>Easy</option>
                    <option value="moderate" <?php echo $difficultyFilter == 'moderate' ? 'selected' : ''; ?>>Moderate</option>
                    <option value="hard" <?php echo $difficultyFilter == 'hard' ? 'selected' : ''; ?>>Hard</option>
                </select>
                <label for="sort">Sort By:</label>
                <select name="sort" id="sort">
                    <option value="none" <?php echo $sortOption == 'none' ? 'selected' : ''; ?>>None</option>
                    <option value="correct" <?php echo $sortOption == 'correct' ? 'selected' : ''; ?>>Highest Answered Correctly</option>
                    <option value="incorrect" <?php echo $sortOption == 'incorrect' ? 'selected' : ''; ?>>Highest Answered Incorrectly</option>
                </select>

                <button class="info-btn"type="submit">Filter</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Question Text</th>
                        <th>Difficulty</th>
                        <th>Answered Correctly</th>
                        <th>Answered Incorrectly</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $question): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($question['question']); ?></td>
                                <td><?php echo htmlspecialchars($question['difficulty']); ?></td>
                                <td><?php echo htmlspecialchars($question['correct_answers']); ?></td>
                                <td><?php echo htmlspecialchars($question['incorrect_answers']); ?></td>
                                <td>
                                    <?php if ($question['archive']): ?>
                                        <button class='info-btn' onclick="if(confirm('Are you sure you want to unarchive this question?')) { window.location.href = 'unarchive_question.php?id=<?php echo $question["question_id"]; ?>'; }">Unarchive</button>
                                    <?php else: ?>
                                        <button class='info-btn' onclick="if(confirm('Are you sure you want to archive this question?')) { window.location.href = 'archive_question.php?id=<?php echo $question["question_id"]; ?>'; }">Archive</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No questions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&difficulty=<?php echo htmlspecialchars($difficultyFilter); ?>&sort=<?php echo htmlspecialchars($sortOption); ?>" class="btn">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&difficulty=<?php echo htmlspecialchars($difficultyFilter); ?>&sort=<?php echo htmlspecialchars($sortOption); ?>" class="btn">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </footer>
</body>
</html>
