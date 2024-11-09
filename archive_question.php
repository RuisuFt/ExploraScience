<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Change query to update the 'archive' field instead of deleting
    $stmt = $conn->prepare("UPDATE questions SET archive = true WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Question archived successfully.";
    } else {
        $_SESSION['error'] = "Error archiving question: " . $stmt->error;
    }

    $stmt->close();
}


header("Location: admin_question_pool.php"); 
exit();
?>
