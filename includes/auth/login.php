<?php
/**
 * Google OAuth Login Handler
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Google OAuth configuration
require_once 'google_config.php';

// Generate a random state parameter to prevent CSRF attacks
$state = bin2hex(random_bytes(16));
$_SESSION['google_auth_state'] = $state;

// Build the authorization URL
$authUrl = GOOGLE_AUTH_URL . '?' . http_build_query([
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URL,
    'response_type' => 'code',
    'scope' => GOOGLE_SCOPES,
    'state' => $state,
    'prompt' => 'select_account', // Always show account selection screen
    'access_type' => 'online'
]);

// Redirect the user to Google's authentication page
header('Location: ' . $authUrl);
exit;