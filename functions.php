<?php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/blogtech/Database.php';
require_once 'redirect.php';

// Check if user is logged in, redirect to login if not
function checkLoggedIn()
{
    if (!isset($_SESSION['user_id'])) {
        redirect("/blogtech/views/registration/login");
    }
}

// Get logged in user data from database
function getLoggedInUser()
{
    global $conn;

    if (isset($_SESSION['user_id'])) {
        // Fetch user with role information
        $stmt = $conn->prepare("SELECT u.*, r.role FROM users u
                                INNER JOIN roles r ON u.role = r.id
                                WHERE u.id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return null;
}

// Check if logged in user is admin
function isAdmin()
{
    $user = getLoggedInUser();
    return $user && $user['role'] === 'admin';
}

// Store admin status for easy access
$is_admin = isAdmin();