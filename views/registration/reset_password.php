<?php
require_once __DIR__ . '/../../errorHandler.php';
require_once __DIR__ . '/../../successHandler.php';

// Ensure the token is coming from both GET and session for redundancy
$token = $_GET['token'] ?? '';
if (!empty($token)) {
    $_SESSION['reset_token'] = $token;
    setcookie('reset_token', $token, time() + 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <div class="title"><span>Reset Password</span></div>
        <?php
// Get the token from GET, session, or cookie
$token = $_GET['token'] ?? ($_SESSION['reset_token'] ?? ($_COOKIE['reset_token'] ?? ''));
?>
<form method="POST" action="/blogtech/views/registration/reset_password">
    <!-- Use multiple token sources for redundancy -->
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="New Password" required>
    </div>
    <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    </div>
    <div class="row button">
        <input type="submit" value="Reset Password">
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