<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Create Account Page</title>
    <script>
        // JavaScript function to validate input fields
        function validateForm(event) {
            const regex = /^[a-zA-Z0-9]*$/;  // This regex allows only letters and numbers
            const firstName = document.querySelector('input[name="first_name"]').value;
            const lastName = document.querySelector('input[name="last_name"]').value;
            const username = document.querySelector('input[name="username"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            // Check if any of the fields contain special characters
            if (!regex.test(firstName) || !regex.test(lastName) || !regex.test(username)) {
                alert("First Name, Last Name, and Username can only contain letters and numbers.");
                event.preventDefault();  // Prevent form submission
                return false;
            }

            // Check username length (5 to 12 characters)
            if (username.length < 5 || username.length > 12) {
                alert("Username must be between 5 and 12 characters long.");
                event.preventDefault();  // Prevent form submission
                return false;
            }

            // Check password and confirm password match
            if (password !== confirmPassword) {
                alert("Password and Confirm Password must match.");
                event.preventDefault();  // Prevent form submission
                return false;
            }

            // Validate password strength
            const passwordStrengthRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,14}$/;
            if (!passwordStrengthRegex.test(password)) {
                alert("Password must contain at least one uppercase letter, one number, one special character, and be between 8 to 14 characters long.");
                event.preventDefault();  // Prevent form submission
                return false;
            }

            return true; // Allow form submission if no issues
        }

        // Real-time password validation feedback (Optional)
        function checkPasswordStrength() {
            const passwordInput = document.querySelector('input[name="password"]');
            const password = passwordInput.value;
            
            // Check individual conditions
            const hasUpperCase = /[A-Z]/.test(password);
            const hasDigit = /\d/.test(password);
            const hasSpecialChar = /[\W_]/.test(password);
            const hasValidLength = password.length >= 8 && password.length <= 14;

            // Update indicators
            document.getElementById('specialCharIndicator').style.color = hasSpecialChar ? 'green' : 'red';
            document.getElementById('upperCaseIndicator').style.color = hasUpperCase ? 'green' : 'red';
            document.getElementById('digitIndicator').style.color = hasDigit ? 'green' : 'red';
            document.getElementById('lengthIndicator').style.color = hasValidLength ? 'green' : 'red';
        }

        // Real-time confirmation password check
        function checkConfirmPassword() {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            const confirmPasswordIndicator = document.getElementById('confirmPasswordIndicator');

            // If confirm password matches password, show green indicator, else red
            if (password === confirmPassword) {
                confirmPasswordIndicator.textContent = "* Passwords match";
                confirmPasswordIndicator.style.color = 'green';
            } else {
                confirmPasswordIndicator.textContent = "* Passwords do not match";
                confirmPasswordIndicator.style.color = 'red';
            }
        }
    </script>
</head>
<body>
    <div class="top-bar">
        <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
        <div class="title-box">
            <h2>Create Account</h2>
        </div>
    </div>
    <div class="body">
        <div class="login-container">
            <div class="login-box">
                <form method="POST" action="create_account_process.php" onsubmit="return validateForm(event)">
                    <!-- First Name -->
                    <div class="input-container">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
                    </div>

                    <!-- Last Name -->
                    <div class="input-container">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
                    </div>

                    <!-- Email -->
                    <div class="input-container">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Email" required>
                    </div>

                    <!-- Username -->
                    <div class="input-container">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Username" required minlength="5" maxlength="12">
                    </div>

                    <!-- Password -->
                    <div class="input-container">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" required oninput="checkPasswordStrength()">
                        
                        <!-- Password Indicators -->
                        <ul id="passwordIndicators">
                            <li id="specialCharIndicator">* Needs 1 Special character</li>
                            <li id="upperCaseIndicator">* Needs 1 Uppercase letter</li>
                            <li id="digitIndicator">* Needs 1 Digit</li>
                            <li id="lengthIndicator">* Needs between 8 and 14 characters</li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-container">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required oninput="checkConfirmPassword()">
                        
                        <!-- Confirm Password Indicator -->
                        <div id="confirmPasswordIndicator" style="font-size: 12px; color: red; margin-top: 5px;"></div>
                    </div>

                    <div class="button-group">
                        <button class="btn" type="button" onclick="window.location.href='index.php'">Return to Login</button>
                        <button class="btn" type="submit">Create Account</button>
                    </div>
                </form>
            </div>
            <div class="logo-right">
                <img src="images/up_signup.png" alt="Secondary Logo">
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
