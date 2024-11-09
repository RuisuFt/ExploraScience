<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['answer']) || !is_array($_POST['answer'])) {
    header("Location: error_page.php"); 
    exit();
}

$answers = $_POST['answer'];
$correct_count = 0; 
$total_points = 0; 
$correct_answers = [];
$wrong_answers = [];

foreach ($answers as $question_id => $user_answer) {
    $query = "SELECT correct_answer, description, difficulty FROM questions WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        $is_correct = ($row['correct_answer'] === $user_answer) ? 1 : 0;
        $correct_count += $is_correct; 

        // Calculate points based on difficulty
        $points = 0;
        switch ($row['difficulty']) {
            case 'easy':
                $points = 1;
                break;
            case 'moderate':
                $points = 2;
                break;
            case 'hard':
                $points = 3;
                break;
        }

        // Insert user answer into user_answers table
        $insert_stmt = $conn->prepare("INSERT INTO user_answers (user_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iisi", $user_id, $question_id, $user_answer, $is_correct);
        $insert_stmt->execute();

        if ($is_correct) {
            $total_points += $points; 
            // Update user's points in the users table
            $update_stmt = $conn->prepare("UPDATE users SET points = points + ? WHERE id = ?");
            $update_stmt->bind_param("ii", $points, $user_id);
            $update_stmt->execute();
        } else {
            // Store incorrect answers for later display
            $wrong_answers[] = [
                'question_id' => $question_id,
                'user_answer' => $user_answer,
                'description' => $row['description'],
            ];
        }

        // Store correct answers for later display
        if ($is_correct) {
            $correct_answers[] = [
                'question_id' => $question_id,
                'user_answer' => $user_answer,
                'description' => $row['description'],
            ];
        }

        $insert_stmt->close();
    }

    $stmt->close();
}

// Store results in session for the results page
$_SESSION['correct_count'] = $correct_count;
$_SESSION['total_points'] = $total_points;
$_SESSION['correct_answers'] = $correct_answers;
$_SESSION['wrong_answers'] = $wrong_answers;

// Redirect to results page
header("Location: results.php");
exit();
?>
