<?php
// Include error handler to manage and display error messages
require_once __DIR__ . '/../../errorHandler.php';
// Include success handler to display success messages
require_once __DIR__ . '/../../successHandler.php';

// Get the reset token from the URL parameter
$token = $_GET['token'] ?? '';

// If we have a token, store it in multiple places for redundancy
// This ensures we don't lose the token if the user refreshes the page
if (!empty($token)) {
    // Store in session for server-side persistence
    $_SESSION['reset_token'] = $token;
    // Store in cookie for additional backup (expires in 1 hour)
    setcookie('reset_token', $token, time() + 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" type="image/png" href="../../icon.png">
    <!-- Custom stylesheet for the reset password page -->
    <link rel="stylesheet" href="../../css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <div class="title"><span>Reset Password</span></div>
        
        <?php
        // Try to get the token from multiple sources (URL, session, or cookie)
        // This fallback chain ensures we can retrieve the token even after page refresh
        $token = $_GET['token'] ?? ($_SESSION['reset_token'] ?? ($_COOKIE['reset_token'] ?? ''));
        ?>
        
        <!-- Form to submit new password -->
        <form method="POST" action="/blogtech/views/registration/reset_password">
            <!-- Hidden field to send the reset token with the form -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <!-- New password input field with lock icon -->
            <div class="row">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="New Password" required>
            </div>
            
            <!-- Confirm password input field to ensure user typed correctly -->
            <div class="row">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            
            <!-- Submit button to reset password -->
            <div class="row button">
                <input type="submit" value="Reset Password">
            </div>

            <?php
            // Check if there are any error messages to display (e.g., passwords don't match, invalid token)
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