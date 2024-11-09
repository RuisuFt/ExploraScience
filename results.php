<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include 'db.php'; // Ensure this file sets up your $conn variable

$correct_count = isset($_SESSION['correct_count']) ? $_SESSION['correct_count'] : 0;
$total_points = isset($_SESSION['total_points']) ? $_SESSION['total_points'] : 0;

$correct_answers = isset($_SESSION['correct_answers']) ? $_SESSION['correct_answers'] : [];
$wrong_answers = isset($_SESSION['wrong_answers']) ? $_SESSION['wrong_answers'] : [];

// Fetch questions and correct answers
$correct_question_ids = array_column($correct_answers, 'question_id');
$wrong_question_ids = array_column($wrong_answers, 'question_id');

$questions = [];
if (!empty($correct_question_ids) || !empty($wrong_question_ids)) {
    $placeholders = rtrim(str_repeat('?,', count($correct_question_ids) + count($wrong_question_ids)), ',');
    $stmt = $conn->prepare("SELECT id, question, choice_A, choice_B, choice_C, choice_D, correct_answer, description FROM questions WHERE id IN ($placeholders)");
    $stmt->execute(array_merge($correct_question_ids, $wrong_question_ids));
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $questions[$row['id']] = $row; // Store entire row for access to all details
    }

    // Prepare to fetch percentages for each question
    $percentages = [];
    foreach ($questions as $question_id => $question) {
        $countQuery = "SELECT COUNT(*) AS total_responses, 
                              SUM(CASE WHEN ua.user_answer = q.correct_answer THEN 1 ELSE 0 END) AS correct_count 
                       FROM user_answers ua
                       JOIN questions q ON ua.question_id = q.id
                       WHERE ua.question_id = ?";
        $countStmt = $conn->prepare($countQuery);
        $countStmt->bind_param("i", $question_id);
        $countStmt->execute();
        $countResult = $countStmt->get_result()->fetch_assoc();

        if ($countResult['total_responses'] > 0) {
            $percentage = ($countResult['correct_count'] / $countResult['total_responses']) * 100;
            $percentages[$question_id] = round($percentage, 2); // Round to 2 decimal places
        } else {
            $percentages[$question_id] = 0; // No responses yet
        }
        
        $countStmt->close();
    }
}

// Clear session variables
unset($_SESSION['correct_count']);
unset($_SESSION['total_points']);
unset($_SESSION['correct_answers']);
unset($_SESSION['wrong_answers']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Your Results</title>
</head>
<body>
    <div class="top-bar">
        <img class="logo-img" src="images/agronomy_logo.png" alt="Logo">
        <div class="title-box">
            <h2>Your Results</h2>
        </div>
    </div>
    <div class="body-results">
        <h3>You got <strong><?php echo $correct_count; ?></strong> correct answers!</h3>
        <h3>Total Points: <strong><?php echo $total_points; ?></strong></h3>

        <h4>Correct Answers:</h4>
        <ul>
            <?php foreach ($correct_answers as $answer): ?>
                <li>
                    <strong>Question: <?php echo htmlspecialchars($questions[$answer['question_id']]['question']); ?></strong>
                    Your answer: <span><?php echo htmlspecialchars($questions[$answer['question_id']]['choice_' . htmlspecialchars($answer['user_answer'])]); ?></span><br>
                    <em>Correct answer: <?php echo htmlspecialchars($questions[$answer['question_id']]['choice_' . htmlspecialchars($questions[$answer['question_id']]['correct_answer'])]); ?></em><br>
                    <em>Description: <?php echo htmlspecialchars($questions[$answer['question_id']]['description']); ?></em><br>
                    <em>Percentage of users who got this right: <?php echo $percentages[$answer['question_id']] . '%'; ?></em>
                </li>
            <?php endforeach; ?>
        </ul>

        <h4>Wrong Answers:</h4>
        <ul>
            <?php foreach ($wrong_answers as $answer): ?>
                <li>
                    <strong>Question: <?php echo htmlspecialchars($questions[$answer['question_id']]['question']); ?></strong>
                    Your answer: <span><?php echo htmlspecialchars($questions[$answer['question_id']]['choice_' . htmlspecialchars($answer['user_answer'])]); ?></span><br>
                    <em>Correct answer: <?php echo htmlspecialchars($questions[$answer['question_id']]['choice_' . htmlspecialchars($questions[$answer['question_id']]['correct_answer'])]); ?></em><br>
                    <em>Description: <?php echo htmlspecialchars($questions[$answer['question_id']]['description']); ?></em><br>
                    <em>Percentage of users who got this right: <?php echo $percentages[$answer['question_id']] . '%'; ?></em>
                </li>
            <?php endforeach; ?>
        </ul>
        <br>
        <br>
        <a href="user_homepage.php" class="btn">Back to Homepage</a>
    </div>
</body>
</html>
