<?php
// Start session
session_start();

// Check if user is logged in
if (isset($_SESSION['user']) && $_SESSION['user']['logged_in']) {
    // If logged in, redirect to home page
    header('Location: home.php');
    exit;
} else {
    // Not logged in, redirect to login page
    header('Location: login.php');
    exit;
}
?>