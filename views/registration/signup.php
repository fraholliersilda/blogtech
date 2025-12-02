<?php
// Include error handler to manage and display error messages
require_once __DIR__ . '/../../errorHandler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIGN UP</title>
  <link rel="icon" type="image/png" href="../../icon.png">
  <!-- Custom stylesheet for the signup page -->
  <link rel="stylesheet" href="../../css/style.css" />
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<body>
  <div class="wrapper">
    <div class="title"><span>SignUp Form</span></div>
    
    <!-- Registration form to create a new user account -->
    <form method="POST" action="/blogtech/views/registration/signup">
      
      <!-- Username input field with user icon -->
      <div class="row">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Username" required />
      </div>
      
      <!-- Email input field with envelope icon -->
      <div class="row">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required />
      </div>
      
      <!-- Password input field with lock icon -->
      <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required />
      </div>
      
      <!-- Submit button to create account -->
      <div class="row button">
        <input type="submit" value="Sign Up" />
      </div>
      
      <!-- Link for existing users to go to login page -->
      <div class="signup-link">Already a member? <a href="login">Login now</a></div>
      
      <?php
      // Check if there are any error messages to display (e.g., username taken, invalid email)
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