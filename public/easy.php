<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Function to retrieve random questions excluding already selected ones
function getUniqueQuestions($difficulty, $limit, $excludedIds = []) {
    global $conn;
    $excludedIdsPlaceholder = !empty($excludedIds) ? " AND id NOT IN (" . implode(',', $excludedIds) . ")" : '';
    
    $query = "SELECT * FROM questions WHERE difficulty = ? $excludedIdsPlaceholder ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $difficulty, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }

    $stmt->close();
    return $questions;
}

// Initialize an array to store selected question IDs
$selectedIds = [];

// Fetch easy questions without duplicates
$easyQuestions = getUniqueQuestions('easy', 10, $selectedIds);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Easy Challenge</title>
</head>
<body>
    <div class="top-bar">
        <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">

        <div class="title-box">
            <h1>Easy Level</h1>
        </div>
        <div class="spacer"></div>
    </div>
    <div class="body-question">
        <form method="POST" action="submit_answer.php">
            <div class="questions-container">
                <?php
                $questionNumber = 1; // Initialize question counter
                foreach ($easyQuestions as $row) {
                    echo "<div class='question'>";
                    
                    // Display the question number and question text
                    echo "<p><strong>Question $questionNumber:</strong> {$row['question']}</p>";
                
                    // Using <label> for clickable letters
                    echo "<input type='radio' id='answer_{$row['id']}_A' name='answer[{$row['id']}]' value='A' required>";
                    echo "<label for='answer_{$row['id']}_A'> A) {$row['choice_A']}</label><br>";
                
                    echo "<input type='radio' id='answer_{$row['id']}_B' name='answer[{$row['id']}]' value='B' required>";
                    echo "<label for='answer_{$row['id']}_B'> B) {$row['choice_B']}</label><br>";
                
                    echo "<input type='radio' id='answer_{$row['id']}_C' name='answer[{$row['id']}]' value='C' required>";
                    echo "<label for='answer_{$row['id']}_C'> C) {$row['choice_C']}</label><br>";
                
                    echo "<input type='radio' id='answer_{$row['id']}_D' name='answer[{$row['id']}]' value='D' required>";
                    echo "<label for='answer_{$row['id']}_D'> D) {$row['choice_D']}</label><br>";
                
                    echo "</div>";

                    $questionNumber++; // Increment the question number after each question
                }
                ?>
            </div>
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
