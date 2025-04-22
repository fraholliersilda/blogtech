<?php
require_once __DIR__ . '/../../errorHandler.php';
require_once __DIR__ . '/../../successHandler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <div class="title"><span>Forgot Password</span></div>
        <form method="POST" action="/blogtech/views/registration/forgot_password">
            <?php
            if (isset($_SESSION['messages']['success']) && !empty($_SESSION['messages']['success'])) {
                echo '<div style="color: #0f5132; background-color: #d1e7dd; border: 1px solid #badbcc; padding: 10px; border-radius: 4px;">';
                displaySuccessMessages();
                echo '</div>';
            }
            ?>
            <div class="row">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="row button">
                <input type="submit" value="Send Reset Link">
            </div>
            <div class="signup-link">
                Remember your password? <a href="login">Login here</a>
            </div>
            <?php
            if (isset($_SESSION['messages']['errors']) && !empty($_SESSION['messages']['errors'])) {
                echo '<div class="error-message">';
                displayErrors();
                echo '</div>';
            }
            ?>
        </form>
    </div>
</body>
</html>