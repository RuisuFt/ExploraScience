<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Prepare statement to fetch the user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists and the password is correct
    if ($user && $password === $user['password']) { 
        // Store user details in session
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];

        // Record the login time in the user_logins table only if the user is not an admin
        if ($user['role'] !== 'admin') {
            $user_id = $user['id'];
            $login_time = date('Y-m-d H:i:s'); // Current timestamp

            // Insert login time into user_logins table
            $log_stmt = $conn->prepare("INSERT INTO user_logins (user_id, login_time) VALUES (?, ?)");
            $log_stmt->bind_param("is", $user_id, $login_time);
            $log_stmt->execute();
            $log_stmt->close();
        }
        
        // Redirect based on user role
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_homepage.php");
        }
    } else {
        echo "<script>alert('Invalid Username or Password'); window.history.back();</script>";
    }

    $stmt->close();
}
?>
