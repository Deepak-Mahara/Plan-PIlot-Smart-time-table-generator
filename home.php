<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Get user data from session
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanPilot - Dashboard</title>
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
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white transition-colors duration-300">
    <!-- Navigation Bar -->
    <nav class="bg-white dark:bg-gray-800 shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo and Brand -->
                <div class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">PlanPilot</span>
                </div>
                
                <!-- Navigation and User Profile -->
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <svg id="dark-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                        <svg id="light-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleDropdown()">
                            <?php if (isset($user['picture']) && $user['picture']): ?>
                                <img src="<?php echo htmlspecialchars($user['picture']); ?>" 
                                     alt="Profile" class="w-8 h-8 rounded-full">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                    <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <span class="font-medium"><?php echo htmlspecialchars($user['given_name'] ?? $user['name'] ?? 'User'); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-10 hidden">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                Profile
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                Account Settings
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            <a href="includes/auth/logout.php" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">
                                Sign out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Welcome Message -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Welcome, <?php echo htmlspecialchars($user['given_name'] ?? $user['name'] ?? 'User'); ?>!</h1>
            <p class="text-gray-600 dark:text-gray-400">Here's your personalized dashboard to manage your schedule.</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <!-- Timetable Card -->
            <a href="interactive_timetable.php" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <div class="p-6">
                    <div class="text-blue-600 dark:text-blue-400 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Interactive Timetable</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">View and manage your personalized schedule with detailed information.</p>
                    <span class="text-blue-500 dark:text-blue-400 flex items-center">
                        View Timetable
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
            </a>
            
            <!-- Create New Timetable -->
            <a href="ai_timetable.php" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <div class="p-6">
                    <div class="text-green-600 dark:text-green-400 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Generate New Timetable</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Create a new AI-powered timetable based on your preferences.</p>
                    <span class="text-green-500 dark:text-green-400 flex items-center">
                        Create New
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
            </a>
            
            <!-- Export PDF -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <div class="p-6">
                    <div class="text-red-600 dark:text-red-400 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Export Timetable</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Download your timetable as PDF for offline reference.</p>
                    <span class="text-red-500 dark:text-red-400 flex items-center">
                        Export PDF
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-semibold mb-6">Recent Activity</h2>
                
                <div class="space-y-4">
                    <!-- Login Activity -->
                    <div class="flex items-start">
                        <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3zm1 0v12h12V3H4zm6 6a1 1 0 100-2 1 1 0 000 2zm3.293 2.293a1 1 0 01-.083 1.32 7.002 7.002 0 01-9.88 0 1 1 0 111.32-1.498 5.002 5.002 0 007.24 0 1 1 0 01.403-.24z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium">Account Login</h4>
                            <p class="text-gray-600 dark:text-gray-400">You logged in successfully</p>
                            <span class="text-xs text-gray-500 dark:text-gray-500">
                                <?php echo date('F j, Y, g:i a', $user['login_time']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 shadow-inner mt-12 py-6">
        <div class="container mx-auto px-4">
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                <p>© 2025 PlanPilot. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for user dropdown and theme toggle -->
    <script>
        // User dropdown toggle
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const userMenu = event.target.closest('.relative');
            if (!userMenu && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });

        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('dark-icon');
        const lightIcon = document.getElementById('light-icon');
        
        themeToggle.addEventListener('click', function() {
            // Toggle dark mode
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            }
        });
        
        // Set initial icon based on current theme
        if (document.documentElement.classList.contains('dark')) {
            darkIcon.classList.remove('hidden');
            lightIcon.classList.add('hidden');
        } else {
            darkIcon.classList.add('hidden');
            lightIcon.classList.remove('hidden');
        }
    </script>
</body>
</html>