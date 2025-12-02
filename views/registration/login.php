<?php
// Include error handler to manage and display error messages
require_once __DIR__ . '/../../errorHandler.php';
// Include success handler to display success messages (like account created successfully)
require_once __DIR__ . '/../../successHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/png" href="../../icon.png">
    <!-- Custom stylesheet for the login page -->
    <link rel="stylesheet" href="../../css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <div class="title"><span>Login Form</span></div>

        <!-- Login form that submits credentials to the server -->
        <form method="POST" action="/blogtech/views/registration/login">
            <?php
            // Check if there are any success messages to display (e.g., registration success, password reset)
            if (isset($_SESSION['messages']['success']) && !empty($_SESSION['messages']['success'])) {
                // Display success message with green styling
                echo '<div style="color: #0f5132; background-color: #d1e7dd; border: 1px solid #badbcc; padding: 10px; border-radius: 4px;">';
                displaySuccessMessages();
                echo '</div>';
            }
            ?>
            
            <!-- Email input field with envelope icon -->
            <div class="row">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Email">
            </div>
            
            <!-- Password input field with lock icon -->
            <div class="row">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password">
            </div>

            <!-- Submit button to login -->
            <div class="row button">
                <input type="submit" value="Login">
            </div>
            
            <!-- Links for new users to sign up and for password recovery -->
            <div class="signup-link">
                Not a member? <a href="signup">Signup now</a>
                <br><a href="forgot_password">Forgot Password?</a>
            </div>

            <?php
            // Check if there are any error messages to display (e.g., invalid credentials)
            if (isset($_SESSION['messages']['errors']) && !empty($_SESSION['messages']['errors'])) {
                // Display error messages
                echo '<div class="error-message">';
                displayErrors();
                echo '</div>';
            }
            ?>

        </form>

    </div>
</body>

</html>