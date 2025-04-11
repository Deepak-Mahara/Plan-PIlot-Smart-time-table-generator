<?php
/**
 * Logout Handler
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear the user session data
unset($_SESSION['user']);

// You can optionally revoke the Google access token here
// if you stored it in the session

// Redirect back to the main page
header('Location: ../../ai_timetable.php?logout=success');
exit;