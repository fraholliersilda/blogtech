<?php
// Include error handler to manage and display error messages
require_once __DIR__ . '/../../errorHandler.php';
// Include success handler to display success messages
require_once __DIR__ . '/../../successHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" type="image/png" href="../../icon.png">
    <!-- Custom stylesheet for the forgot password page -->
    <link rel="stylesheet" href="../../css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <div class="title"><span>Forgot Password</span></div>
        
        <!-- Form to submit email for password reset -->
        <form method="POST" action="/blogtech/views/registration/forgot_password">
            <?php
            // Check if there are any success messages to display
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
                <input type="email" name="email" placeholder="Enter your email address" required>
            </div>
            
            <!-- Submit button to send password reset link -->
            <div class="row button">
                <input type="submit" value="Send Reset Link">
            </div>
            
            <!-- Link to go back to login page if user remembers password -->
            <div class="signup-link">
                Remember your password? <a href="login">Login here</a>
            </div>
            
            <?php
            // Check if there are any error messages to display
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