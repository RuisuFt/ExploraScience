<?php
session_start();
include 'db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Initialize variables for filtering and pagination
$difficultyFilter = isset($_GET['difficulty']) ? $_GET['difficulty'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Function to validate CSV file format
function validate_csv_format($data) {
    // Check if the number of columns is correct
    if (count($data) !== 10) { // Adjust this number according to your expected columns
        return false;
    }
    // Additional format checks can go here (e.g., data types)
    return true;
}

// CSV Import Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== FALSE) {
        fgetcsv($handle); // Skip the header row
        $importSuccess = true; // Flag for success

        while (($data = fgetcsv($handle)) !== FALSE) {
            // Validate the CSV format
            if (!validate_csv_format($data)) {
                $_SESSION['error'] = "Invalid CSV format.";
                $importSuccess = false;
                break;
            }

            // Prepare and execute your SQL statement
            $stmt = $conn->prepare("
                INSERT INTO questions (id, question, choice_A, choice_B, choice_C, choice_D, correct_answer, difficulty, description, archive) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    question = VALUES(question), 
                    choice_A = VALUES(choice_A), 
                    choice_B = VALUES(choice_B), 
                    choice_C = VALUES(choice_C), 
                    choice_D = VALUES(choice_D), 
                    correct_answer = VALUES(correct_answer), 
                    difficulty = VALUES(difficulty), 
                    description = VALUES(description), 
                    archive = VALUES(archive)"
            );

            // Bind parameters; check if description is null
            if (empty($data[8])) { // Assuming description is at index 8
                $_SESSION['error'] = "Description cannot be null for ID: {$data[0]}";
                $importSuccess = false;
                break;
            }

            $stmt->bind_param("issssssssi", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9]);
            if (!$stmt->execute()) {
                $_SESSION['error'] = "Error importing question with ID: {$data[0]} - " . $stmt->error;
                $importSuccess = false;
                break;
            }
        }
        fclose($handle);

        if ($importSuccess) {
            $_SESSION['message'] = "Questions imported successfully.";
        }
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit;
    } else {
        $_SESSION['error'] = "Error opening the file.";
    }
}

// Fetching questions with filtering and pagination
$query = "SELECT * FROM questions WHERE archive = 0"; // Exclude archived questions
if ($difficultyFilter != 'all') {
    $query .= " AND difficulty = ?"; // Adjust for difficulty filter
}
$query .= " LIMIT ?, ?"; // Add LIMIT clause for pagination

$stmt = $conn->prepare($query);

// Bind parameters for the query
if ($difficultyFilter != 'all') {
    $stmt->bind_param("sii", $difficultyFilter, $offset, $limit);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Check if result is not false
if ($result === false) {
    echo "Error in query execution: " . $conn->error;
    exit;
}

// Count total questions for pagination
$countQuery = "SELECT COUNT(*) as total FROM questions WHERE archive = 0"; // Exclude archived questions
if ($difficultyFilter != 'all') {
    $countQuery .= " AND difficulty = ?";
}
$countStmt = $conn->prepare($countQuery);
if ($difficultyFilter != 'all') {
    $countStmt->bind_param("s", $difficultyFilter);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Handle edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    // Get the form data
    $id = $_POST['id'];
    $question = $_POST['question'];
    $choice_A = $_POST['choice_A'];
    $choice_B = $_POST['choice_B'];
    $choice_C = $_POST['choice_C'];
    $choice_D = $_POST['choice_D'];
    $correct_answer = $_POST['correct_answer'];
    $difficulty = $_POST['difficulty'];
    $description = $_POST['description'];

    // Update the question in the database
    $stmt = $conn->prepare("
        UPDATE questions 
        SET question = ?, choice_A = ?, choice_B = ?, choice_C = ?, choice_D = ?, correct_answer = ?, difficulty = ?, description = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("ssssssssi", $question, $choice_A, $choice_B, $choice_C, $choice_D, $correct_answer, $difficulty, $description, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Question updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating question: " . $stmt->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
// Export CSV Logic
if (isset($_GET['export'])) {
    // Create a query to fetch all questions (you can modify the query if needed)
    $query = "SELECT * FROM questions WHERE archive = 0"; // Exclude archived questions
    if ($difficultyFilter != 'all') {
        $query .= " AND difficulty = ?";
    }

    // Prepare the query
    $stmt = $conn->prepare($query);
    if ($difficultyFilter != 'all') {
        $stmt->bind_param("s", $difficultyFilter);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any results
    if ($result->num_rows > 0) {
        // Output headers to prompt download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="questions_data_format.csv"');
        $output = fopen('php://output', 'w');

        // Write the CSV headers
        fputcsv($output, ['ID', 'Question', 'Choice A', 'Choice B', 'Choice C', 'Choice D', 'Correct Answer', 'Difficulty', 'Description', 'Archive']);

        // Write each row to the CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    } else {
        // No questions found, you can handle it here (e.g., show an error message)
        $_SESSION['error'] = "No questions found to export.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

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
            <button class="btn" onclick="window.location.href='admin_accounts.php'">Accounts</button>
            <button class="btn" style="background-color: #888;" onclick="window.location.href='admin_question_pool.php'">Questions Pool</button>
            <button class="btn" onclick="window.location.href='admin_archive_questions.php'">Archive Questions</button>
            <button class="btn" onclick="window.location.href='admin_question_stat.php'">Questions Statistics</button>
            <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
            </div>
    </div>

    <div class="body">
        <div class="admin-container">
            <h3>Question Pool:</h3>

            <!-- Export Button -->
            <form method="GET" action="">
                <button type="submit" name="export" class="info-btn">Export CSV</button>
            </form>


            <!-- Import Form -->
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="csv_file">Import CSV: <input type="file" name="csv_file" accept=".csv" required></label>
                <input type="submit" value="Import" class="info-btn">
            </form>

            <!-- Existing Filter Form -->
            <form method="GET" action="">
                <label for="difficulty">Filter by Difficulty:</label>
                <select name="difficulty" id="difficulty">
                    <option value="all" <?php echo $difficultyFilter == 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="easy" <?php echo $difficultyFilter == 'easy' ? 'selected' : ''; ?>>Easy</option>
                    <option value="moderate" <?php echo $difficultyFilter == 'moderate' ? 'selected' : ''; ?>>Moderate
                    </option>
                    <option value="hard" <?php echo $difficultyFilter == 'hard' ? 'selected' : ''; ?>>Hard</option>
                </select>
                <button type="submit" class="info-btn">Filter</button>
            </form>

            <h1>Manage Questions</h1>
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
                        <th style="width: 250px;">Actions</th>
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
    <button class='info-btn' onclick=\"if(confirm('Are you sure you want to archive this question?')) { window.location.href = 'archive_question.php?id={$row["id"]}'; }\">
        Archive
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
                    <a href="?page=<?php echo $page - 1; ?>&difficulty=<?php echo $difficultyFilter; ?>"
                        class="btn">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&difficulty=<?php echo $difficultyFilter; ?>"
                        class="btn">Next</a>
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
        window.onload = function() {
        <?php if (isset($_SESSION['message'])): ?>
            alert("<?php echo addslashes($_SESSION['message']); ?>");
            <?php unset($_SESSION['message']); // Clear the message after displaying ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            alert("<?php echo addslashes($_SESSION['error']); ?>");
            <?php unset($_SESSION['error']); // Clear the error after displaying ?>
        <?php endif; ?>
    };
    </script>

</body>

</html>