<?php
/**
 * Google OAuth Configuration
 */

// Include environment variable loader
require_once dirname(__DIR__) . '/env_loader.php';

// Google OAuth Client ID and Secret from environment variables
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET'));

// Redirect URL after Google authentication (must match one configured in Google Developer Console)
define('GOOGLE_REDIRECT_URL', 'http://localhost/Project/includes/auth/callback.php');

// OAuth authorization URL
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');

// Scopes requested for Google API
define('GOOGLE_SCOPES', 'email profile');