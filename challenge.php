<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Retrieve student level from the database
$username = $_SESSION['username'];
$query = "SELECT level_number FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $studentLevel = $row['level_number']; // Use level_number as student level
} else {
    // Handle the case where the user is not found in the database
    die("User not found.");
}

$stmt->close();

// Define difficulty constants, implementing rules
define('EASY', 'easy');
define('MEDIUM', 'medium');
define('HARD', 'hard');

// Functions/rules to generate questions adjusted to the student's level
function generateQuestions($studentLevel) {
    $totalQuestions = 10; // We need to display 10 questions
    $questions = [];
    $selectedIds = []; // Array to store selected question IDs

    // Determine initial distribution based on student level
    if ($studentLevel >= 0 && $studentLevel <= 3) {
        $numEasy = 7;
        $numMedium = 2;
        $numHard = 1;
    } elseif ($studentLevel >= 4 && $studentLevel <= 7) {
        $numEasy = 3;
        $numMedium = 5;
        $numHard = 2;
    } elseif ($studentLevel >= 8 && $studentLevel <= 10) {
        $numEasy = 1;
        $numMedium = 3;
        $numHard = 6;
    } else {
        // Handle cases where level_number is out of expected range
        return []; // No questions can be generated
    }

    // Attempt to retrieve the questions by difficulty
    $easyQuestions = generateQuestionsByDifficulty(EASY, $numEasy, $selectedIds);
    $mediumQuestions = generateQuestionsByDifficulty(MEDIUM, $numMedium, $selectedIds);
    $hardQuestions = generateQuestionsByDifficulty(HARD, $numHard, $selectedIds);

    // Merge the questions and check if we have less than 10 questions
    $questions = array_merge($easyQuestions, $mediumQuestions, $hardQuestions);

    // Check if we have less than 10 questions
    if (count($questions) < $totalQuestions) {
        // Calculate how many more questions are needed
        $needed = $totalQuestions - count($questions);
        $extraQuestions = generateAdditionalQuestions($needed, $selectedIds);
        $questions = array_merge($questions, $extraQuestions);
    }

    // Shuffle questions to randomize their order
    shuffle($questions);

    // Return exactly 10 questions
    return array_slice($questions, 0, $totalQuestions);
}

function generateQuestionsByDifficulty($difficulty, $count, &$selectedIds) {
    global $conn;
    $questions = [];
    
    // Create a placeholder for the question IDs to exclude
    $excludedIds = implode(',', $selectedIds);
    $query = "SELECT * FROM questions WHERE difficulty = ?";

    // If there are already selected IDs, append to the query to exclude them
    if (!empty($excludedIds)) {
        $query .= " AND id NOT IN ($excludedIds)";
    }
    
    $query .= " ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $difficulty, $count);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
            $selectedIds[] = $row['id']; // Add the selected question ID to the array
        }
    } else {
        error_log("Database query failed: " . $conn->error);
    }
    
    $stmt->close();
    return $questions;
}

function generateAdditionalQuestions($needed, &$selectedIds) {
    global $conn;
    $extraQuestions = [];
    
    // Create a placeholder for the question IDs to exclude
    $excludedIds = implode(',', $selectedIds);
    $query = "SELECT * FROM questions";

    // If there are already selected IDs, append to the query to exclude them
    if (!empty($excludedIds)) {
        $query .= " WHERE id NOT IN ($excludedIds)";
    }

    $query .= " ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $needed);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $extraQuestions[] = $row;
            $selectedIds[] = $row['id']; // Add the selected question ID to the array
        }
    } else {
        error_log("Database query failed: " . $conn->error);
    }
    
    $stmt->close();
    return $extraQuestions;
}

// Generate 10 questions based on the student level
$questions = generateQuestions($studentLevel);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Challenge</title>
</head>
<body>
    <div class="top-bar">
        <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
        <div class="title-box">
            <h1>Challenge</h1>
        </div>
        <div class="spacer"></div>
    </div>
    <div class="body-question">
        <form method="POST" action="submit_answer.php">
            <div class="questions-container">
                <?php
                $questionNumber = 1; // Initialize question counter
                foreach ($questions as $question) {
                    echo "<div class='question'>";
                    
                    // Display the question number and question text
                    echo "<p><strong>Question $questionNumber:</strong> {$question['question']}</p>";
                
                    // Use <label> for clickable letters
                    echo "<input type='radio' id='answer_{$question['id']}_A' name='answer[{$question['id']}]' value='A' required>";
                    echo "<label for='answer_{$question['id']}_A'> A) {$question['choice_A']}</label><br>";
                
                    echo "<input type='radio' id='answer_{$question['id']}_B' name='answer[{$question['id']}]' value='B' required>";
                    echo "<label for='answer_{$question['id']}_B'> B) {$question['choice_B']}</label><br>";
                
                    echo "<input type='radio' id='answer_{$question['id']}_C' name='answer[{$question['id']}]' value='C' required>";
                    echo "<label for='answer_{$question['id']}_C'> C) {$question['choice_C']}</label><br>";
                
                    echo "<input type='radio' id='answer_{$question['id']}_D' name='answer[{$question['id']}]' value='D' required>";
                    echo "<label for='answer_{$question['id']}_D'> D) {$question['choice_D']}</label><br>";
                
                    echo "</div>";
                    
                    $questionNumber++; // Increment the question number after each question
                }
                ?>
            </div>
            <br>
            <button class="btn" type="submit">Submit All Answers</button>
        </form>
    </div>

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
