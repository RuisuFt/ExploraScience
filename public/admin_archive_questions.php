<?php
session_start();
include 'db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Update the question logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $question = $_POST['question'];
    $choice_A = $_POST['choice_A'];
    $choice_B = $_POST['choice_B'];
    $choice_C = $_POST['choice_C'];
    $choice_D = $_POST['choice_D'];
    $correct_answer = $_POST['correct_answer'];
    $difficulty = $_POST['difficulty'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE questions SET question=?, choice_A=?, choice_B=?, choice_C=?, choice_D=?, correct_answer=?, difficulty=?, description=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $question, $choice_A, $choice_B, $choice_C, $choice_D, $correct_answer, $difficulty, $description, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Question updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating question: " . $stmt->error;
    }

    $stmt->close();
}

// Pagination and Filtering Logic
$limit = 10; // Number of questions per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get the selected difficulty from the filter
$difficultyFilter = isset($_GET['difficulty']) ? $_GET['difficulty'] : 'all';

// Build the query based on the difficulty filter and archive status
$query = "SELECT * FROM questions WHERE archive = true"; // Only show archived questions
if ($difficultyFilter !== 'all') {
    $query .= " AND difficulty = ?";
}
$query .= " LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($query);
if ($difficultyFilter !== 'all') {
    $stmt->bind_param("s", $difficultyFilter);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch the total number of archived questions based on the filter
$totalQuery = "SELECT COUNT(*) as total FROM questions WHERE archive = true"; // Only count archived questions
if ($difficultyFilter !== 'all') {
    $totalQuery .= " AND difficulty = ?";
}
$totalStmt = $conn->prepare($totalQuery);
if ($difficultyFilter !== 'all') {
    $totalStmt->bind_param("s", $difficultyFilter);
}
$totalStmt->execute();
$totalRow = $totalStmt->get_result()->fetch_assoc();
$totalQuestions = $totalRow['total'];
$totalPages = ceil($totalQuestions / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin - Archived Questions</title>
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
        <button class="btn" onclick="window.location.href='admin_accounts.php'">Accounts</button>
        <button class="btn" onclick="window.location.href='admin_question_pool.php'">Questions Pool</button>
        <button class="btn" style="background-color: #888;" onclick="window.location.href='admin_archive_questions.php'">Archive Questions</button>
        <button class="btn" onclick="window.location.href='admin_question_stat.php'">Questions Statistics</button>
        <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
        </div>
</div>

<div class="body">
    <div class="admin-container">
        <h3>Archived Question Pool:</h3>

        <!-- Filter Form -->
        <form method="GET" action="">
            <label for="difficulty">Filter by Difficulty:</label>
            <select name="difficulty" id="difficulty">
                <option value="all" <?php echo $difficultyFilter == 'all' ? 'selected' : ''; ?>>All</option>
                <option value="easy" <?php echo $difficultyFilter == 'easy' ? 'selected' : ''; ?>>Easy</option>
                <option value="moderate" <?php echo $difficultyFilter == 'moderate' ? 'selected' : ''; ?>>Moderate</option>
                <option value="hard" <?php echo $difficultyFilter == 'hard' ? 'selected' : ''; ?>>Hard</option>
            </select>
            <button type="submit" class="info-btn">Filter</button>
        </form>

        <h1>Manage Archived Questions</h1>
        <div class="table-responsive">
        <table>
                    <tr>
                        <th>Question</th>
                        <th style="width: 85px;">Choice A</th>
                        <th style="width: 85px;">Choice B</th>
                        <th style="width: 85px;">Choice C</th>
                        <th style="width: 85px;">Choice D</th>
                        <th style="width: 60px;">Correct Answer</th>
                        <th>Difficulty</th>
                        <th>Description For Answer</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                    <?php
                    // Check if there are results before looping
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['question']}</td>
                                <td>{$row['choice_A']}</td>
                                <td>{$row['choice_B']}</td>
                                <td>{$row['choice_C']}</td>
                                <td>{$row['choice_D']}</td>
                                <td>{$row['correct_answer']}</td>
                                <td>{$row['difficulty']}</td>
                                <td>{$row['description']}</td>
                                <td>
    <button class='info-btn'onclick='openEditPopup({$row["id"]}, \"" . addslashes($row['question']) . "\", \"" . addslashes($row['choice_A']) . "\", \"" . addslashes($row['choice_B']) . "\", \"" . addslashes($row['choice_C']) . "\", \"" . addslashes($row['choice_D']) . "\", \"" . addslashes($row['correct_answer']) . "\", \"" . addslashes($row['difficulty']) . "\", \"" . addslashes($row['description']) . "\")'>
        Edit
    </button>
    <button class='info-btn' onclick=\"if(confirm('Are you sure you want to unarchive this question?')) { window.location.href = 'unarchive_question.php?id={$row["id"]}'; }\">
        Unarchive
    </button>
</td>
                              </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No questions found.</td></tr>";
                    }
                    ?>
                </table>
        </div>

        <!-- Pagination Buttons -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&difficulty=<?php echo $difficultyFilter; ?>" class="btn">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&difficulty=<?php echo $difficultyFilter; ?>" class="btn">Next</a>
            <?php endif; ?>
            <a href="admin_create_question.php" class="btn">Create Question</a>
        </div>
    </div>
</div>

<div id="editPopup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border:1px solid #ccc; padding:20px; z-index:100;">
    <h2>Edit Question</h2>
    <form id="editForm" method="post" action="">
        <input type="hidden" name="id" id="editId" value="">

        <label>Question: <input type="text" name="question" id="editQuestion" required></label><br>
        <label>Choice A: <input type="text" name="choice_A" id="editChoiceA" required></label><br>
        <label>Choice B: <input type="text" name="choice_B" id="editChoiceB" required></label><br>
        <label>Choice C: <input type="text" name="choice_C" id="editChoiceC" required></label><br>
        <label>Choice D: <input type="text" name="choice_D" id="editChoiceD" required></label><br>

        <label>Correct Answer: 
            <select name="correct_answer" id="editCorrectAnswer" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </label><br>

        <label>Difficulty: 
            <select name="difficulty" id="editDifficulty" required>
                <option value="easy">Easy</option>
                <option value="moderate">Moderate</option>
                <option value="hard">Hard</option>
            </select>
        </label><br>

        <label>Description: 
            <textarea name="description" id="editDescription" required></textarea>
        </label><br>

        <input type="submit" value="Save">
        <button type="button" onclick="closeEditPopup()">Cancel</button>
    </form>
</div>

<script>
function openEditPopup(id, question, choiceA, choiceB, choiceC, choiceD, correctAnswer, difficulty, description) {
    document.getElementById('editId').value = id;
    document.getElementById('editQuestion').value = question;
    document.getElementById('editChoiceA').value = choiceA;
    document.getElementById('editChoiceB').value = choiceB;
    document.getElementById('editChoiceC').value = choiceC;
    document.getElementById('editChoiceD').value = choiceD;
    document.getElementById('editCorrectAnswer').value = correctAnswer;
    document.getElementById('editDifficulty').value = difficulty;
    document.getElementById('editDescription').value = description;
    
    document.getElementById('editPopup').style.display = 'block';
}

function closeEditPopup() {
    document.getElementById('editPopup').style.display = 'none';
}
</script>
</body>
</html>
