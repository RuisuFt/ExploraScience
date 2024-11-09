<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question'];
    $choiceA = $_POST['choice_A'];
    $choiceB = $_POST['choice_B'];
    $choiceC = $_POST['choice_C'];
    $choiceD = $_POST['choice_D'];
    $correctAnswer = $_POST['correct_answer'];
    $difficulty = $_POST['difficulty'];
    $description = $_POST['description'];  // New description field

    $insert_query = "INSERT INTO questions (question, choice_A, choice_B, choice_C, choice_D, correct_answer, difficulty, description) 
                     VALUES ('$question_text', '$choiceA', '$choiceB', '$choiceC', '$choiceD', '$correctAnswer', '$difficulty', '$description')";

    if ($conn->query($insert_query) === TRUE) {
        echo "New question created successfully!";
        header("Location: admin_create_question.php");
        exit();
    } else {
        echo "Error creating question: " . $conn->error;
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
        <button class="btn" onclick="window.location.href='admin_question_pool.php'">Questions Pool</button>
        <button class="btn" onclick="window.location.href='admin_archive_questions.php'">Archive Questions</button>
        <button class="btn" onclick="window.location.href='admin_question_stat.php'">Questions Statistics</button>
        <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
        </div>
</div>
<div class="body">
    <div class="admin-container">
        <h3>Input Questions:</h3>
        <ul>
            <form method="POST">
                <label>Question:</label><br>
                <input type="text" name="question" required><br>

                <label>Choice A:</label><br>
                <input type="text" name="choice_A" required><br>

                <label>Choice B:</label><br>
                <input type="text" name="choice_B" required><br>

                <label>Choice C:</label><br>
                <input type="text" name="choice_C" required><br>

                <label>Choice D:</label><br>
                <input type="text" name="choice_D" required><br>

                <label>Correct Answer:</label><br>
                <select name="correct_answer" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select><br>

                <label>Difficulty:</label><br>
                <select name="difficulty" required>
                    <option value="easy">Easy</option>
                    <option value="moderate">Moderate</option>
                    <option value="hard">Hard</option>
                </select><br>

                <!-- New Description Field -->
                <label>Description (Why is this the correct answer?):</label><br>
                <textarea name="description" rows="4" cols="50" required></textarea><br>

                <button class="btn" type="submit">Create Question</button>
            </form>
        </ul>
    </div>
</div>

<footer>
    <div class="bottom-bar">
        <form>
        <div class="contact-info">
            <div class="email">Email: contact@example.com</div>
            <div class="address">Street Address: 123 Example St, City, Country</div>
        </div>
    </div>
</footer>
</body>
</html>

