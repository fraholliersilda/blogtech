<?php

// Store errors in session
function setErrors($errors)
{
    $_SESSION['messages']['errors'] = $errors;
}

// Retrieve and clear errors from session
function getErrors()
{
    if (isset($_SESSION['messages']['errors'])) {
        $errors = $_SESSION['messages']['errors'];
        unset($_SESSION['messages']['errors']);
        return $errors;
    }
    return [];
}

// Display all errors as HTML
function displayErrors()
{
    $errors = getErrors();
    if (!empty($errors)) {
        echo '<div class="error-messages">';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
}