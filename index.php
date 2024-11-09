<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login Page</title>
</head>
<body>
    <div class="top-bar">
        <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
        <div class="title-box">
            <h2>Login Account</h2>
        </div>
        <div class="spacer"></div>
    </div>
    <div class="body">
        <div class="login-container">
            <div class="login-box">
                <form method="POST" action="login_process.php">
                    <div class="input-container">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Username" required minlength="5" maxlength="12">
                    </div>
                    <div class="input-container">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
                <br>
                <button onclick="window.location.href='terms_and_condition.php'">Sign Up</button>
            </div>
            <div class="logo-right">
                <img src="images/up_login.png" alt="Logo">
            </div>
        </div>
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
