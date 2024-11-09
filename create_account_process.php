<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $level = 'Beginner';
    $role = 'user';

    // Validate email domain (only Gmail and Yahoo are allowed)
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com)$/", $email)) {
        echo "<script>alert('Please use yahoo or gmail for email information'); window.history.back();</script>";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    // Check if the username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If a record exists with the same username or email
        echo "<script>alert('Username or Email already exists.'); window.history.back();</script>";
        exit;
    }

    // If no record exists, proceed with the insert
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password, email, level, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $first_name, $last_name, $username, $password, $email, $level, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Account Created Successfully'); window.location='index.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
