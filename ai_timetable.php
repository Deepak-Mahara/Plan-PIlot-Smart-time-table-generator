<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to store timetable data
session_start();

// Include API integration
require_once 'includes/gemini_api.php';

// Process form submission
$aiResponse = null;
$error = null;
$debugInfo = null;
$formSubmitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_ai'])) {
    $formSubmitted = true;
    
    // Store the form data in the session
    $_SESSION['timetable_data'] = $_POST;
    
    // Call Gemini API
    $aiResponse = generatePersonalizedTimetable($_POST);
    
    if (!$aiResponse['success']) {
        $error = $aiResponse['error'] ?? 'Failed to generate personalized timetable.';
        // Store debug info for display
        $debugInfo = $aiResponse['debug_info'] ?? [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Timetable Generator</title>
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
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="container mx-auto px-4 py-8">
        <header class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">PlanPilot Timetable</h1>
                    <p class="text-gray-600 dark:text-gray-300">Intelligently optimized schedule for maximum productivity</p>
                </div>
                
                <!-- User Authentication Section -->
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['logged_in']): ?>
                        <!-- User is logged in -->
                        <div class="flex items-center bg-white dark:bg-gray-800 p-2 rounded-lg shadow-md">
                            <?php if (isset($_SESSION['user']['picture']) && $_SESSION['user']['picture']): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['user']['picture']); ?>" 
                                     alt="Profile" class="w-8 h-8 rounded-full mr-2">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white mr-2">
                                    <?php echo strtoupper(substr($_SESSION['user']['name'] ?? 'U', 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mr-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'User'); ?>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>
                                </div>
                            </div>
                            
                            <a href="includes/auth/logout.php" 
                               class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <a href="includes/auth/login.php" class="flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <!-- Google logo -->
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                                    <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                                    <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                                    <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                                    <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
                                </g>
                            </svg>
                            Sign in with Google
                        </a>
                    <?php endif; ?>
                    
                    <!-- Dark/Light Mode Toggle -->
                    <div>
                        <button id="theme-toggle" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-gray-800 dark:text-white flex items-center justify-center transition-colors duration-300">
                            <!-- Sun Icon for Light Mode -->
                            <svg id="dark-mode-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                            <span id="dark-mode-text" class="hidden md:inline ml-1">Dark</span>
                            
                            <!-- Moon Icon for Dark Mode -->
                            <svg id="light-mode-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                            </svg>
                            <span id="light-mode-text" class="hidden md:inline ml-1 hidden">Light</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if (isset($_GET['login']) && $_GET['login'] == 'success'): ?>
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <p>You have successfully logged in with Google.</p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['auth_error'])): ?>
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p>Authentication error: <?php echo htmlspecialchars($_SESSION['auth_error'] ?? 'Unknown error'); ?></p>
                </div>
            <?php endif; ?>
        </header>

        <main>
            <!-- Navigation Tabs -->
            <div class="mb-8 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <a href="home.php" class="inline-block py-4 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-b-2 border-transparent">
                            Dashboard
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="interactive_timetable.php" class="inline-block py-4 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-b-2 border-transparent">
                            Interactive Timetable
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="#" class="inline-block py-4 px-4 text-sm font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400" aria-current="page">
                            Generate New Timetable
                        </a>
                    </li>
                </ul>
            </div>

            <?php if (!$formSubmitted || (isset($error) && $error)): ?>
            <!-- AI Input Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">Generate Your AI Timetable</h2>
                    
                    <?php if (isset($error) && $error): ?>
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 dark:bg-red-900 dark:text-red-300">
                            <p class="font-medium">Error: <?php echo htmlspecialchars($error); ?></p>
                            <?php if (isset($debugInfo) && is_array($debugInfo) && !empty($debugInfo)): ?>
                                <pre class="mt-2 text-xs overflow-x-auto"><?php echo htmlspecialchars(json_encode($debugInfo, JSON_PRETTY_PRINT)); ?></pre>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="" id="ai-timetable-form" class="space-y-6">
                        <!-- Learning Preferences Section -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg mb-6">
                            <h3 class="text-xl font-medium mb-4 text-gray-800 dark:text-white">Learning Preferences</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="learning_style" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Your Learning Style
                                    </label>
                                    <select id="learning_style" name="learning_style" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option value="visual">Visual Learner</option>
                                        <option value="auditory">Auditory Learner</option>
                                        <option value="kinesthetic">Hands-on/Kinesthetic Learner</option>
                                        <option value="reading">Reading/Writing Learner</option>
                                        <option value="mixed">Mixed Learning Style</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="productivity_peak" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Peak Productivity Time
                                    </label>
                                    <select id="productivity_peak" name="productivity_peak" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option value="early_morning">Early Morning (6AM-9AM)</option>
                                        <option value="morning">Morning (9AM-12PM)</option>
                                        <option value="afternoon">Afternoon (12PM-4PM)</option>
                                        <option value="evening">Evening (4PM-8PM)</option>
                                        <option value="night">Night (8PM onwards)</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="special_requirements" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Any Special Learning Requirements?
                                    </label>
                                    <textarea id="special_requirements" name="special_requirements" rows="2" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="E.g., Need longer breaks for complex subjects, prefer group studies for certain courses, etc."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Time Preferences Section -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg mb-6">
                            <h3 class="text-xl font-medium mb-4 text-gray-800 dark:text-white">Time Preferences</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Day Starts At
                                    </label>
                                    <select id="start_time" name="start_time" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <?php 
                                        $start_times = [
                                            '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', 
                                            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00'
                                        ];
                                        foreach ($start_times as $time): 
                                        ?>
                                            <option value="<?php echo $time; ?>"><?php echo $time; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="end_time" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Day Ends At
                                    </label>
                                    <select id="end_time" name="end_time" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <?php 
                                        $end_times = [
                                            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', 
                                            '16:00', '16:30', '17:00', '17:30', '18:00', '18:30',
                                            '19:00', '19:30', '20:00', '20:30', '21:00'
                                        ];
                                        foreach ($end_times as $time): 
                                        ?>
                                            <option value="<?php echo $time; ?>"><?php echo $time; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="max_classes_per_day" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Maximum Classes Per Day
                                    </label>
                                    <select id="max_classes_per_day" name="max_classes_per_day" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <?php for ($i = 2; $i <= 8; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="break_duration" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Preferred Break Between Classes (minutes)
                                    </label>
                                    <select id="break_duration" name="break_duration" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option value="0">No break needed</option>
                                        <option value="10">10 minutes</option>
                                        <option value="15">15 minutes</option>
                                        <option value="30">30 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="60">1 hour</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="lunch_time" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Preferred Lunch Time
                                    </label>
                                    <select id="lunch_time" name="lunch_time" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option value="11:30">11:30 - 12:30</option>
                                        <option value="12:00">12:00 - 13:00</option>
                                        <option value="12:30">12:30 - 13:30</option>
                                        <option value="13:00">13:00 - 14:00</option>
                                        <option value="13:30">13:30 - 14:30</option>
                                        <option value="none">No lunch break needed</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="lunch_duration" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Lunch Break Duration (minutes)
                                    </label>
                                    <select id="lunch_duration" name="lunch_duration" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option value="30">30 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="60" selected>1 hour</option>
                                        <option value="90">1 hour 30 minutes</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 flex flex-wrap gap-4">
                                    <div class="flex items-center">
                                        <input id="prefer_morning" name="prefer_morning" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="prefer_morning" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Prefer morning classes when possible
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="group_similar_subjects" name="group_similar_subjects" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="group_similar_subjects" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Group similar subjects on same day
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="distribute_evenly" name="distribute_evenly" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="distribute_evenly" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Distribute workload evenly across week
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                                             
                        <!-- Course Section -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-xl font-medium mb-4 text-gray-800 dark:text-white">Courses</h3>
                            <div id="courses-container" class="space-y-6">
                                <div class="course-entry p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="courses[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Course Name
                                            </label>
                                            <input type="text" id="courses[0]" name="courses[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="e.g. Mathematics, Physics">
                                        </div>
                                        <div>
                                            <label for="instructors[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Instructor/Professor
                                            </label>
                                            <input type="text" id="instructors[0]" name="instructors[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="e.g. Prof. Anderson">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label for="credits[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Credit Hours
                                            </label>
                                            <select id="credits[0]" name="credits[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="frequency[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Sessions per Week
                                            </label>
                                            <select id="frequency[0]" name="frequency[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="locations[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Room/Location
                                            </label>
                                            <input type="text" id="locations[0]" name="locations[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="e.g. Room 207, Lab 2">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="session_length[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Preferred Session Length
                                            </label>
                                            <select id="session_length[0]" name="session_length[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                <option value="30">30 minutes</option>
                                                <option value="45">45 minutes</option>
                                                <option value="60">1 hour</option>
                                                <option value="90" selected>1 hour 30 minutes</option>
                                                <option value="120">2 hours</option>
                                                <option value="180">3 hours</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="subject_difficulty[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Subject Complexity
                                            </label>
                                            <select id="subject_difficulty[0]" name="subject_difficulty[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                <option value="easy">Easy - Schedule Anytime</option>
                                                <option value="moderate" selected>Moderate - Better during alert hours</option>
                                                <option value="hard">Challenging - Need peak focus times</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <label for="course_notes[0]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Special Notes for this Class (Optional)
                                        </label>
                                        <textarea id="course_notes[0]" name="course_notes[]" rows="2" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="E.g., Midterm on Week 7, Final review session needed, Lab equipment preparation"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="button" id="add-course" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Another Course
                                </button>
                            </div>
                        </div>
                        
                        <!-- Conflicts Section -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 class="text-xl font-medium mb-2 text-gray-800 dark:text-white">Conflicts & Blocked Times</h3>
                            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Add any time slots that you're not available for classes</p>
                            
                            <div id="conflicts-container" class="space-y-4">
                                <!-- This will be filled dynamically -->
                            </div>
                            
                            <div class="mt-4">
                                <button type="button" id="add-conflict" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Time Conflict
                                </button>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-center">
                            <button type="submit" name="generate_ai" class="py-3 px-6 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-base flex items-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Generate AI Timetable
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
                <!-- Show results from AI timetable generation -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Your AI-Generated Timetable</h2>
                            <button onclick="window.location.href='ai_timetable.php'" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                                Generate Another
                            </button>
                        </div>
                        
                        <!-- Interactive Timetable Display -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Monday</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tuesday</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Wednesday</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Thursday</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Friday</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <?php
                                    // Get all time slots from the timetable
                                    $allTimeSlots = array();
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                                    
                                    if (isset($aiResponse['timetable']) && is_array($aiResponse['timetable'])) {
                                        foreach ($aiResponse['timetable'] as $day => $slots) {
                                            if (is_array($slots)) {
                                                foreach ($slots as $time => $details) {
                                                    $allTimeSlots[$time] = true;
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Sort time slots
                                    ksort($allTimeSlots);
                                    $timeSlots = array_keys($allTimeSlots);
                                    
                                    // Display each time slot
                                    foreach ($timeSlots as $time) {
                                        echo '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">';
                                        echo '<td class="py-4 px-4 font-medium">' . htmlspecialchars($time) . '</td>';
                                        
                                        foreach ($days as $day) {
                                            echo '<td class="py-4 px-4">';
                                            if (isset($aiResponse['timetable'][$day][$time])) {
                                                $courseDetails = $aiResponse['timetable'][$day][$time];
                                                if (is_array($courseDetails) && isset($courseDetails['course'])) {
                                                    $courseName = htmlspecialchars($courseDetails['course']);
                                                    $instructor = isset($courseDetails['instructor']) ? htmlspecialchars($courseDetails['instructor']) : '';
                                                    $location = isset($courseDetails['location']) ? htmlspecialchars($courseDetails['location']) : '';
                                                    $notes = isset($courseDetails['notes']) ? htmlspecialchars($courseDetails['notes']) : '';
                                                    $sessionText = '';
                                                    if (isset($courseDetails['session'])) {
                                                        $sessionText = " (Session " . htmlspecialchars($courseDetails['session']) . ")";
                                                    }
                                                    
                                                    echo '<div class="p-2 bg-blue-100 dark:bg-blue-900 rounded text-blue-800 dark:text-blue-200">';
                                                    echo '<div class="font-medium">' . $courseName . $sessionText . '</div>';
                                                    
                                                    if ($instructor) {
                                                        echo '<div class="text-xs mt-1">' . $instructor;
                                                        if ($location) {
                                                            echo ' • ' . $location;
                                                        }
                                                        echo '</div>';
                                                    } elseif ($location) {
                                                        echo '<div class="text-xs mt-1">' . $location . '</div>';
                                                    }
                                                    
                                                    if ($notes) {
                                                        echo '<div class="text-xs mt-1 italic">Notes: ' . $notes . '</div>';
                                                    }
                                                    
                                                    echo '</div>';
                                                } else {
                                                    echo '<span class="text-gray-500 dark:text-gray-400">No Class</span>';
                                                }
                                            } else {
                                                echo '<span class="text-gray-400 dark:text-gray-500">-</span>';
                                            }
                                            echo '</td>';
                                        }
                                        
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Save and Export Options -->
                        <div class="mt-8 flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="includes/generate_ai_pdf.php" target="_blank" class="py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-center flex-1 sm:flex-none flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Export as PDF
                            </a>
                            <button type="button" onclick="window.location.href='interactive_timetable.php'" class="py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-center flex-1 sm:flex-none flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save & Edit in Interactive Mode
                            </button>
                            <button type="button" id="print-timetable" class="py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg text-center flex-1 sm:flex-none flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print Timetable
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Insights Section -->
                <?php if (isset($aiResponse['insights']) && !empty($aiResponse['insights'])): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Personalization Insights</h3>
                        <ul class="list-disc pl-5 space-y-2">
                            <?php foreach ($aiResponse['insights'] as $insight): ?>
                                <li class="text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($insight); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Recommendations Section -->
                <?php if (isset($aiResponse['recommendations']) && !empty($aiResponse['recommendations'])): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Study Recommendations</h3>
                        <ul class="list-disc pl-5 space-y-2">
                            <?php foreach ($aiResponse['recommendations'] as $recommendation): ?>
                                <li class="text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($recommendation); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Explanation Section -->
                <?php if (isset($aiResponse['explanation']) && !empty($aiResponse['explanation'])): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Schedule Rationale</h3>
                        <div class="text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
                            <?php echo nl2br(htmlspecialchars($aiResponse['explanation'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 shadow-inner mt-12 py-6">
        <div class="container mx-auto px-4">
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                <p>© 2025 PlanPilot. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Theme toggling functionality
        document.getElementById('theme-toggle').addEventListener('click', function() {
            const darkModeIcon = document.getElementById('dark-mode-icon');
            const lightModeIcon = document.getElementById('light-mode-icon');
            const darkModeText = document.getElementById('dark-mode-text');
            const lightModeText = document.getElementById('light-mode-text');
            
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
                darkModeIcon.classList.add('hidden');
                lightModeIcon.classList.remove('hidden');
                darkModeText.classList.add('hidden');
                lightModeText.classList.remove('hidden');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
                darkModeIcon.classList.remove('hidden');
                lightModeIcon.classList.add('hidden');
                darkModeText.classList.remove('hidden');
                lightModeText.classList.add('hidden');
            }
        });
        
        // Set initial theme state
        if (document.documentElement.classList.contains('dark')) {
            document.getElementById('dark-mode-icon').classList.remove('hidden');
            document.getElementById('light-mode-icon').classList.add('hidden');
            document.getElementById('dark-mode-text').classList.remove('hidden');
            document.getElementById('light-mode-text').classList.add('hidden');
        } else {
            document.getElementById('dark-mode-icon').classList.add('hidden');
            document.getElementById('light-mode-icon').classList.remove('hidden');
            document.getElementById('dark-mode-text').classList.add('hidden');
            document.getElementById('light-mode-text').classList.remove('hidden');
        }
        
        // Add Course Button Functionality
        document.getElementById('add-course').addEventListener('click', function() {
            const coursesContainer = document.getElementById('courses-container');
            const courseCount = coursesContainer.querySelectorAll('.course-entry').length;
            
            const courseTemplate = `
                <div class="course-entry p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="courses[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Course Name
                            </label>
                            <input type="text" id="courses[${courseCount}]" name="courses[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="e.g. Mathematics, Physics">
                        </div>
                        <div>
                            <label for="instructors[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Instructor/Professor
                            </label>
                            <input type="text" id="instructors[${courseCount}]" name="instructors[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="e.g. Prof. Anderson">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="credits[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Credit Hours
                            </label>
                            <select id="credits[${courseCount}]" name="credits[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                ${[1, 2, 3, 4, 5, 6].map(i => `<option value="${i}">${i}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label for="frequency[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Sessions per Week
                            </label>
                            <select id="frequency[${courseCount}]" name="frequency[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                ${[1, 2, 3, 4, 5].map(i => `<option value="${i}">${i}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label for="locations[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Room/Location
                            </label>
                            <input type="text" id="locations[${courseCount}]" name="locations[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="e.g. Room 207, Lab 2">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="session_length[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Preferred Session Length
                            </label>
                            <select id="session_length[${courseCount}]" name="session_length[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="90" selected>1 hour 30 minutes</option>
                                <option value="120">2 hours</option>
                                <option value="180">3 hours</option>
                            </select>
                        </div>
                        <div>
                            <label for="subject_difficulty[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Subject Complexity
                            </label>
                            <select id="subject_difficulty[${courseCount}]" name="subject_difficulty[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="easy">Easy - Schedule Anytime</option>
                                <option value="moderate" selected>Moderate - Better during alert hours</option>
                                <option value="hard">Challenging - Need peak focus times</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="course_notes[${courseCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Special Notes for this Class (Optional)
                        </label>
                        <textarea id="course_notes[${courseCount}]" name="course_notes[]" rows="2" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="E.g., Midterm on Week 7, Final review session needed, Lab equipment preparation"></textarea>
                    </div>
                </div>
            `;
            
            // Create temporary element to hold the template
            const div = document.createElement('div');
            div.innerHTML = courseTemplate.trim();
            
            // Append the new course entry
            coursesContainer.appendChild(div.firstChild);
        });
        
        // Add Conflict Button Functionality
        document.getElementById('add-conflict').addEventListener('click', function() {
            const conflictsContainer = document.getElementById('conflicts-container');
            const conflictCount = document.querySelectorAll('[name="conflict_day[]"]').length;
            
            const conflictTemplate = `
                <div class="conflict-entry p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="conflict_day[${conflictCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Day
                            </label>
                            <select id="conflict_day[${conflictCount}]" name="conflict_day[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                            </select>
                        </div>
                        <div>
                            <label for="conflict_start[${conflictCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Start Time
                            </label>
                            <select id="conflict_start[${conflictCount}]" name="conflict_start[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                ${Array.from({length: 16}, (_, i) => i + 7).map(hour => `
                                    <option value="${String(hour).padStart(2, '0')}:00">${String(hour).padStart(2, '0')}:00</option>
                                    <option value="${String(hour).padStart(2, '0')}:30">${String(hour).padStart(2, '0')}:30</option>
                                `).join('')}
                            </select>
                        </div>
                        <div>
                            <label for="conflict_end[${conflictCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                End Time
                            </label>
                            <select id="conflict_end[${conflictCount}]" name="conflict_end[]" required class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                ${Array.from({length: 16}, (_, i) => i + 7).map(hour => `
                                    <option value="${String(hour).padStart(2, '0')}:00">${String(hour).padStart(2, '0')}:00</option>
                                    <option value="${String(hour).padStart(2, '0')}:30">${String(hour).padStart(2, '0')}:30</option>
                                `).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="conflict_reason[${conflictCount}]" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Reason (Optional)
                        </label>
                        <input type="text" id="conflict_reason[${conflictCount}]" name="conflict_reason[]" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="E.g., Part-time job, gym, club meeting">
                    </div>
                </div>
            `;
            
            // Create temporary element to hold the template
            const div = document.createElement('div');
            div.innerHTML = conflictTemplate.trim();
            
            // Append the new conflict entry
            conflictsContainer.appendChild(div.firstChild);
        });
        
        // Print timetable functionality
        if (document.getElementById('print-timetable')) {
            document.getElementById('print-timetable').addEventListener('click', function() {
                window.print();
            });
        }
        
        // Form validation
        document.getElementById('ai-timetable-form')?.addEventListener('submit', function(event) {
            const coursesCount = document.querySelectorAll('input[name="courses[]"]').length;
            let isValid = true;
            
            // Check if at least one course has been added
            if (coursesCount === 0) {
                alert('Please add at least one course');
                isValid = false;
            }
            
            // Check if any course name is empty
            document.querySelectorAll('input[name="courses[]"]').forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>