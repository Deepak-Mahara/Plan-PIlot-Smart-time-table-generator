<?php
// Start session
session_start();

// Check if user is already logged in, redirect to home page
if (isset($_SESSION['user']) && $_SESSION['user']['logged_in']) {
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanPilot - Login</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Dark mode configuration -->
    <script>
        // Check for dark mode preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300 min-h-screen flex items-center justify-center">
    <div class="container max-w-md mx-auto px-4 py-8">
        <!-- Login Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <!-- Header with Logo -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-center">
                <h1 class="text-3xl font-bold text-white">PlanPilot</h1>
                <p class="text-blue-100 mt-2">Intelligent Timetable Management</p>
            </div>
            
            <!-- Login Content -->
            <div class="p-8">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6 text-center">Welcome Back</h2>
                
                <?php if (isset($_GET['auth_error'])): ?>
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p>Authentication error: <?php echo htmlspecialchars($_SESSION['auth_error'] ?? 'Unknown error'); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <p>You have successfully logged out.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Google Login Button -->
                <div class="flex flex-col items-center space-y-4">
                    <p class="text-gray-600 dark:text-gray-300 mb-4 text-center">
                        Sign in to access your personalized timetable and schedule
                    </p>
                    
                    <a href="includes/auth/login.php" class="flex items-center justify-center w-full px-4 py-3 bg-white border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <!-- Google logo -->
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                                <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                                <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                                <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                                <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
                            </g>
                        </svg>
                        <span class="text-gray-700 dark:text-gray-200 font-medium">Sign in with Google</span>
                    </a>
                </div>
                
                <!-- Additional Information -->
                <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
                    <p>By signing in, you agree to our Terms and Privacy Policy</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-8 py-4 bg-gray-50 dark:bg-gray-700 text-center">
                <button id="theme-toggle" class="text-sm text-gray-600 dark:text-gray-300 hover:underline focus:outline-none">
                    <span id="theme-icon" class="inline-block w-4 h-4 mr-1">🌙</span>
                    <span id="theme-text">Switch to Light Mode</span>
                </button>
            </div>
        </div>
        
        <!-- Credits -->
        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
            <p>© 2025 PlanPilot. All Rights Reserved.</p>
        </div>
    </div>
    
    <!-- JavaScript for theme toggling -->
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const themeText = document.getElementById('theme-text');
        
        themeToggle.addEventListener('click', function() {
            // Toggle dark mode
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
                themeIcon.innerText = '☀️';
                themeText.innerText = 'Switch to Dark Mode';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
                themeIcon.innerText = '🌙';
                themeText.innerText = 'Switch to Light Mode';
            }
        });
        
        // Set initial text based on current theme
        if (document.documentElement.classList.contains('dark')) {
            themeIcon.innerText = '🌙';
            themeText.innerText = 'Switch to Light Mode';
        } else {
            themeIcon.innerText = '☀️';
            themeText.innerText = 'Switch to Dark Mode';
        }
    </script>
</body>
</html>