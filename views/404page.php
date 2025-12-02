<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Custom stylesheet for the 404 error page with animated cogs -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/404.css">
  <title>404 Error</title>
  <link rel="icon" type="image/png" href="../../icon.png">
</head>

<body>
  <div class="container">
    <!-- First "4" in the 404 display -->
    <h1 class="first-four">4</h1>
    
    <!-- First animated cog wheel that forms the "0" in 404 -->
    <div class="cog-wheel1">
      <div class="cog1">
        <!-- Individual parts of the cog gear creating the spinning animation -->
        <div class="top"></div>
        <div class="down"></div>
        <div class="left-top"></div>
        <div class="left-down"></div>
        <div class="right-top"></div>
        <div class="right-down"></div>
        <div class="left"></div>
        <div class="right"></div>
      </div>
    </div>

    <!-- Second animated cog wheel that forms the "0" in 404 -->
    <div class="cog-wheel2">
      <div class="cog2">
        <!-- Individual parts of the cog gear creating the spinning animation -->
        <div class="top"></div>
        <div class="down"></div>
        <div class="left-top"></div>
        <div class="left-down"></div>
        <div class="right-top"></div>
        <div class="right-down"></div>
        <div class="left"></div>
        <div class="right"></div>
      </div>
    </div>
    
    <!-- Second "4" in the 404 display -->
    <h1 class="second-four">4</h1>
    
    <!-- Error message and back button -->
    <p class="wrong-para">
      Uh Oh! Page not found!
      <br>
      <!-- Button that takes user back to the previous page -->
      <button class="back-button" onclick="history.back()">Go Back</button>
    </p>

  </div>
</body>

</html>