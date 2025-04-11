<?php
/**
 * Google OAuth Callback Handler
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Google OAuth configuration
require_once 'google_config.php';

// Check if there's an error in the callback
if (isset($_GET['error'])) {
    $_SESSION['auth_error'] = $_GET['error'];
    header('Location: ../../login.php?auth_error=true');
    exit;
}

// Validate state parameter to prevent CSRF attacks
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['google_auth_state']) {
    $_SESSION['auth_error'] = 'Invalid state parameter. Possible CSRF attack.';
    header('Location: ../../login.php?auth_error=true');
    exit;
}

// Check for the authorization code
if (!isset($_GET['code'])) {
    $_SESSION['auth_error'] = 'Authorization code not received.';
    header('Location: ../../login.php?auth_error=true');
    exit;
}

$code = $_GET['code'];

// Exchange the authorization code for access tokens
$tokenUrl = 'https://oauth2.googleapis.com/token';
$data = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URL,
    'grant_type' => 'authorization_code'
];

// Use cURL to make the token request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for development, remove in production
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    $_SESSION['auth_error'] = 'cURL error: ' . curl_error($ch);
    curl_close($ch);
    header('Location: ../../login.php?auth_error=true');
    exit;
}
curl_close($ch);

// Check for API errors
if ($httpCode !== 200) {
    $_SESSION['auth_error'] = 'Google API returned error code: ' . $httpCode;
    header('Location: ../../login.php?auth_error=true');
    exit;
}

// Parse the token response
$tokenData = json_decode($response, true);
if (!isset($tokenData['access_token'])) {
    $_SESSION['auth_error'] = 'Access token not received.';
    header('Location: ../../login.php?auth_error=true');
    exit;
}

// Use access token to get user info from Google
$userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenData['access_token']]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for development, remove in production
$userInfo = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    $_SESSION['auth_error'] = 'cURL error: ' . curl_error($ch);
    curl_close($ch);
    header('Location: ../../login.php?auth_error=true');
    exit;
}
curl_close($ch);

// Check for API errors
if ($httpCode !== 200) {
    $_SESSION['auth_error'] = 'Google User Info API returned error code: ' . $httpCode;
    header('Location: ../../login.php?auth_error=true');
    exit;
}

// Parse user info
$userData = json_decode($userInfo, true);
if (!isset($userData['email'])) {
    $_SESSION['auth_error'] = 'User email not received.';
    header('Location: ../../login.php?auth_error=true');
    exit;
}

// Store user info in session
$_SESSION['user'] = [
    'logged_in' => true,
    'id' => $userData['id'] ?? null,
    'email' => $userData['email'],
    'name' => $userData['name'] ?? null,
    'given_name' => $userData['given_name'] ?? null,
    'family_name' => $userData['family_name'] ?? null,
    'picture' => $userData['picture'] ?? null,
    'locale' => $userData['locale'] ?? null,
    'access_token' => $tokenData['access_token'],
    'login_time' => time()
];

// Redirect to home page with success message
header('Location: ../../home.php?login=success');
exit;