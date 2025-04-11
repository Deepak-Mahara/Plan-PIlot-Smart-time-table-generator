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

// Sample timetable data (in a real application, this would come from a database)
$timetable = [
    'Monday' => [
        ['type' => 'class', 'subject' => 'Mathematics', 'time' => '08:30 - 10:00', 'room' => 'Room 101', 'teacher' => 'Prof. Johnson', 'notes' => 'Chapter 7 & 8'],
        ['type' => 'break', 'description' => 'Short Break', 'time' => '10:00 - 10:15'],
        ['type' => 'class', 'subject' => 'Computer Science', 'time' => '10:15 - 11:45', 'room' => 'Lab 3', 'teacher' => 'Dr. Smith', 'notes' => 'Bring laptop'],
        ['type' => 'break', 'description' => 'Lunch', 'time' => '11:45 - 12:45'],
        ['type' => 'class', 'subject' => 'Physics', 'time' => '12:45 - 14:15', 'room' => 'Room 205', 'teacher' => 'Prof. Williams', 'notes' => 'Lab session']
    ],
    'Tuesday' => [
        ['type' => 'class', 'subject' => 'English', 'time' => '09:00 - 10:30', 'room' => 'Room 103', 'teacher' => 'Ms. Davis', 'notes' => 'Essay review'],
        ['type' => 'break', 'description' => 'Short Break', 'time' => '10:30 - 10:45'],
        ['type' => 'class', 'subject' => 'History', 'time' => '10:45 - 12:15', 'room' => 'Room 207', 'teacher' => 'Prof. Anderson', 'notes' => 'Midterm prep'],
        ['type' => 'break', 'description' => 'Lunch', 'time' => '12:15 - 13:15'],
        ['type' => 'study', 'subject' => 'Self Study', 'time' => '13:15 - 15:15', 'room' => 'Library', 'notes' => 'Group project work']
    ],
    'Wednesday' => [
        ['type' => 'class', 'subject' => 'Mathematics', 'time' => '08:30 - 10:00', 'room' => 'Room 101', 'teacher' => 'Prof. Johnson', 'notes' => 'Chapter 9'],
        ['type' => 'break', 'description' => 'Short Break', 'time' => '10:00 - 10:15'],
        ['type' => 'class', 'subject' => 'Chemistry', 'time' => '10:15 - 11:45', 'room' => 'Lab 2', 'teacher' => 'Dr. Brown', 'notes' => 'Lab experiment'],
        ['type' => 'break', 'description' => 'Lunch', 'time' => '11:45 - 12:45'],
        ['type' => 'class', 'subject' => 'Computer Science', 'time' => '12:45 - 14:15', 'room' => 'Lab 3', 'teacher' => 'Dr. Smith', 'notes' => 'Project presentation']
    ],
    'Thursday' => [
        ['type' => 'class', 'subject' => 'Physics', 'time' => '09:00 - 10:30', 'room' => 'Room 205', 'teacher' => 'Prof. Williams', 'notes' => 'Chapter 6 review'],
        ['type' => 'break', 'description' => 'Short Break', 'time' => '10:30 - 10:45'],
        ['type' => 'class', 'subject' => 'English', 'time' => '10:45 - 12:15', 'room' => 'Room 103', 'teacher' => 'Ms. Davis', 'notes' => 'Presentation skills'],
        ['type' => 'break', 'description' => 'Lunch', 'time' => '12:15 - 13:15'],
        ['type' => 'extracurricular', 'subject' => 'Debate Club', 'time' => '13:15 - 15:15', 'room' => 'Auditorium', 'notes' => 'Weekly meeting']
    ],
    'Friday' => [
        ['type' => 'class', 'subject' => 'History', 'time' => '08:30 - 10:00', 'room' => 'Room 207', 'teacher' => 'Prof. Anderson', 'notes' => 'Final review'],
        ['type' => 'break', 'description' => 'Short Break', 'time' => '10:00 - 10:15'],
        ['type' => 'class', 'subject' => 'Chemistry', 'time' => '10:15 - 11:45', 'room' => 'Lab 2', 'teacher' => 'Dr. Brown', 'notes' => 'Test'],
        ['type' => 'break', 'description' => 'Lunch', 'time' => '11:45 - 12:45'],
        ['type' => 'free', 'description' => 'Free Time', 'time' => '12:45 - 15:00', 'notes' => 'Optional study hall available']
    ]
];

// Get current day of the week
$today = date('l');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Timetable - PlanPilot</title>
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
                <div class="flex items-center space-x-4">
                    <a href="home.php" class="flex items-center space-x-2">
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">PlanPilot</span>
                    </a>
                    
                    <!-- Navigation Links -->
                    <div class="hidden md:flex space-x-4 ml-6">
                        <a href="home.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
                        <a href="interactive_timetable.php" class="text-blue-600 dark:text-blue-400 font-medium">Timetable</a>
                        <a href="ai_timetable.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Generate New</a>
                    </div>
                </div>
                
                <!-- User Profile and Theme Toggle -->
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
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleUserDropdown()">
                            <?php if (isset($user['picture']) && $user['picture']): ?>
                                <img src="<?php echo htmlspecialchars($user['picture']); ?>" alt="Profile" class="w-8 h-8 rounded-full">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                    <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <span class="font-medium hidden md:inline"><?php echo htmlspecialchars($user['given_name'] ?? $user['name'] ?? 'User'); ?></span>
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
                                Settings
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Your Interactive Timetable</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">View and manage your personalized class schedule</p>
        </div>
        
        <!-- Day Selection Tabs -->
        <div class="mb-6">
            <div class="flex flex-wrap space-x-1 border-b border-gray-200 dark:border-gray-700">
                <?php foreach (array_keys($timetable) as $day): ?>
                <button 
                    onclick="switchDay('<?php echo $day; ?>')" 
                    class="day-tab px-4 py-2 font-medium rounded-t-lg <?php echo ($day == $today) ? 'bg-blue-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?>"
                    id="tab-<?php echo strtolower($day); ?>">
                    <?php echo $day; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Timetable Display -->
        <?php foreach ($timetable as $day => $schedule): ?>
        <div id="day-<?php echo strtolower($day); ?>" class="day-content <?php echo ($day != $today) ? 'hidden' : ''; ?>">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold"><?php echo $day; ?></h2>
                        <div class="flex space-x-2">
                            <button onclick="window.print();" class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-md hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-sm">Print</button>
                            <a href="includes/generate_ai_pdf.php" target="_blank" class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-md hover:bg-green-200 dark:hover:bg-green-800 transition-colors text-sm inline-block">Export as PDF</a>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <?php foreach ($schedule as $item): ?>
                            <?php if ($item['type'] == 'class'): ?>
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col md:flex-row justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-400"><?php echo htmlspecialchars($item['subject']); ?></h3>
                                            <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($item['teacher']); ?> • <?php echo htmlspecialchars($item['room']); ?></p>
                                        </div>
                                        <div class="mt-2 md:mt-0">
                                            <span class="inline-block text-gray-600 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($item['time']); ?></span>
                                        </div>
                                    </div>
                                    <?php if (isset($item['notes']) && $item['notes']): ?>
                                        <div class="mt-2 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300">Notes:</span> <?php echo htmlspecialchars($item['notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-3 flex space-x-2">
                                        <button class="text-xs px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors" onclick="showDetails('<?php echo htmlspecialchars(json_encode($item)); ?>')">
                                            Details
                                        </button>
                                    </div>
                                </div>
                            <?php elseif ($item['type'] == 'break'): ?>
                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border-l-4 border-green-500">
                                    <div class="flex flex-col md:flex-row justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-green-700 dark:text-green-400"><?php echo htmlspecialchars($item['description']); ?></h3>
                                        </div>
                                        <div class="mt-2 md:mt-0">
                                            <span class="inline-block text-gray-600 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($item['time']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($item['type'] == 'study'): ?>
                                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col md:flex-row justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-purple-700 dark:text-purple-400"><?php echo htmlspecialchars($item['subject']); ?></h3>
                                            <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($item['room']); ?></p>
                                        </div>
                                        <div class="mt-2 md:mt-0">
                                            <span class="inline-block text-gray-600 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($item['time']); ?></span>
                                        </div>
                                    </div>
                                    <?php if (isset($item['notes']) && $item['notes']): ?>
                                        <div class="mt-2 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300">Notes:</span> <?php echo htmlspecialchars($item['notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($item['type'] == 'extracurricular'): ?>
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col md:flex-row justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-yellow-700 dark:text-yellow-400"><?php echo htmlspecialchars($item['subject']); ?></h3>
                                            <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($item['room']); ?></p>
                                        </div>
                                        <div class="mt-2 md:mt-0">
                                            <span class="inline-block text-gray-600 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($item['time']); ?></span>
                                        </div>
                                    </div>
                                    <?php if (isset($item['notes']) && $item['notes']): ?>
                                        <div class="mt-2 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300">Notes:</span> <?php echo htmlspecialchars($item['notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($item['type'] == 'free'): ?>
                                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border-l-4 border-gray-500">
                                    <div class="flex flex-col md:flex-row justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($item['description']); ?></h3>
                                        </div>
                                        <div class="mt-2 md:mt-0">
                                            <span class="inline-block text-gray-600 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($item['time']); ?></span>
                                        </div>
                                    </div>
                                    <?php if (isset($item['notes']) && $item['notes']): ?>
                                        <div class="mt-2 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300">Notes:</span> <?php echo htmlspecialchars($item['notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Summary Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-2">Weekly Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total Classes:</span>
                        <span class="font-medium">15</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total Hours:</span>
                        <span class="font-medium">22.5</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Free Periods:</span>
                        <span class="font-medium">3</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-2">Upcoming Deadlines</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between">
                        <span class="text-red-600 dark:text-red-400">Physics Lab Report</span>
                        <span class="text-gray-600 dark:text-gray-400">Tomorrow</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Math Assignment</span>
                        <span class="text-gray-600 dark:text-gray-400">In 3 days</span>
                    </li>
                    <li class="flex justify-between">
                        <span>History Presentation</span>
                        <span class="text-gray-600 dark:text-gray-400">In 5 days</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-2">Break Distribution</h3>
                <div class="h-32 flex items-end justify-around mt-2">
                    <div class="w-10 bg-blue-500 dark:bg-blue-600" style="height: 60%;" title="Monday: 1h 15m"></div>
                    <div class="w-10 bg-blue-500 dark:bg-blue-600" style="height: 80%;" title="Tuesday: 1h 45m"></div>
                    <div class="w-10 bg-blue-500 dark:bg-blue-600" style="height: 60%;" title="Wednesday: 1h 15m"></div>
                    <div class="w-10 bg-blue-500 dark:bg-blue-600" style="height: 80%;" title="Thursday: 1h 45m"></div>
                    <div class="w-10 bg-blue-500 dark:bg-blue-600" style="height: 100%;" title="Friday: 2h 30m"></div>
                </div>
                <div class="flex justify-around text-xs text-gray-600 dark:text-gray-400 mt-2">
                    <span>Mon</span>
                    <span>Tue</span>
                    <span>Wed</span>
                    <span>Thu</span>
                    <span>Fri</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Course Details -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <h3 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-white"></h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-4 space-y-4">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Time:</span>
                        <span id="modalTime" class="font-medium"></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Location:</span>
                        <span id="modalRoom" class="font-medium"></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Instructor:</span>
                        <span id="modalTeacher" class="font-medium"></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Notes:</span>
                        <p id="modalNotes" class="text-gray-800 dark:text-gray-200"></p>
                    </div>
                </div>
                <div class="mt-6 flex space-x-3">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">Add Reminder</button>
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition-colors">Close</button>
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

    <!-- JavaScript for timetable functionality -->
    <script>
        // Function to switch between days
        function switchDay(day) {
            // Hide all day content
            document.querySelectorAll('.day-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Show selected day content
            document.getElementById('day-' + day.toLowerCase()).classList.remove('hidden');
            
            // Update tab styles
            document.querySelectorAll('.day-tab').forEach(tab => {
                tab.classList.remove('bg-blue-500', 'text-white');
                tab.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            });
            
            // Style active tab
            document.getElementById('tab-' + day.toLowerCase()).classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            document.getElementById('tab-' + day.toLowerCase()).classList.add('bg-blue-500', 'text-white');
        }
        
        // Function to toggle user dropdown
        function toggleUserDropdown() {
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
        
        // Show course details modal
        function showDetails(itemJson) {
            const item = JSON.parse(itemJson);
            document.getElementById('modalTitle').textContent = item.subject;
            document.getElementById('modalTime').textContent = item.time;
            document.getElementById('modalRoom').textContent = item.room;
            document.getElementById('modalTeacher').textContent = item.teacher;
            document.getElementById('modalNotes').textContent = item.notes || 'No additional notes';
            
            document.getElementById('detailsModal').classList.remove('hidden');
            document.getElementById('detailsModal').classList.add('flex');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
            document.getElementById('detailsModal').classList.remove('flex');
            document.body.style.overflow = ''; // Restore scrolling
        }
        
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
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target === modal) {
                closeModal();
            }
        });
        
        // Escape key to close modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>