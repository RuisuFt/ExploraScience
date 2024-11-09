<?php
session_start();
include 'db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Unarchive the question
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Update the 'archive' field to false to unarchive the question
    $stmt = $conn->prepare("UPDATE questions SET archive = false WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Question unarchived successfully.";
    } else {
        $_SESSION['error'] = "Error unarchiving question: " . $stmt->error;
    }

    $stmt->close();
}

// Redirect back to the question pool
header("Location: admin_question_pool.php"); 
exit();
?>
