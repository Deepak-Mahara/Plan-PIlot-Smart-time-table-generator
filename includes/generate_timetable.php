<?php
// Main timetable generation logic

// Only process if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    // Collect input data
    $courses = $_POST['courses'] ?? [];
    $credits = $_POST['credits'] ?? [];
    $frequency = $_POST['frequency'] ?? [];
    $startTime = $_POST['start_time'] ?? '08:00';
    $endTime = $_POST['end_time'] ?? '18:00';
    $conflictDays = $_POST['conflict_day'] ?? [];
    $conflictStarts = $_POST['conflict_start'] ?? [];
    $conflictEnds = $_POST['conflict_end'] ?? [];
    $preferMorning = isset($_POST['prefer_morning']) ? true : false;
    $maxClassesPerDay = $_POST['max_classes_per_day'] ?? 5;
    $breakDuration = $_POST['break_duration'] ?? 15;

    // Validate inputs
    if (empty($courses)) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            Please add at least one course.
        </div>';
        return;
    }

    // Process time inputs to hour format for calculations
    $startHour = intval(substr($startTime, 0, 2));
    $startMinute = intval(substr($startTime, 3, 2));
    $endHour = intval(substr($endTime, 0, 2));
    $endMinute = intval(substr($endTime, 3, 2));
    
    // Calculate total available hours
    $startTimeDecimal = $startHour + ($startMinute / 60);
    $endTimeDecimal = $endHour + ($endMinute / 60);
    $totalHours = $endTimeDecimal - $startTimeDecimal;
    
    if ($totalHours <= 0) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            End time must be after start time.
        </div>';
        return;
    }
    
    // Define days of the week
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    
    // Define time slots (1-hour blocks)
    $timeSlots = [];
    $currentTime = $startTimeDecimal;
    while ($currentTime < $endTimeDecimal) {
        $hour = floor($currentTime);
        $minute = floor(($currentTime - $hour) * 60);
        
        $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT);
        $formattedMinute = str_pad($minute, 2, '0', STR_PAD_LEFT);
        
        $timeSlots[] = "$formattedHour:$formattedMinute";
        $currentTime += 1; // 1-hour blocks
    }
    
    // Process conflicts into a structure
    $conflicts = [];
    foreach ($days as $day) {
        $conflicts[$day] = [];
    }
    
    for ($i = 0; $i < count($conflictDays); $i++) {
        if (isset($conflictDays[$i]) && isset($conflictStarts[$i]) && isset($conflictEnds[$i])) {
            $day = $conflictDays[$i];
            $start = $conflictStarts[$i];
            $end = $conflictEnds[$i];
            
            if (!empty($start) && !empty($end)) {
                $conflicts[$day][] = [
                    'start' => $start,
                    'end' => $end
                ];
            }
        }
    }

    // Initialize the timetable grid
    $timetable = [];
    foreach ($days as $day) {
        $timetable[$day] = [];
        foreach ($timeSlots as $slot) {
            $timetable[$day][$slot] = null;
        }
    }
    
    // Initialize course colors (for visual distinction)
    $courseColors = [];
    foreach ($courses as $index => $course) {
        $colorIndex = ($index % 8) + 1;
        $courseColors[$course] = "course-color-$colorIndex";
    }
    
    // Create a collection of all classes that need to be scheduled based on frequency
    $classesToSchedule = [];
    for ($i = 0; $i < count($courses); $i++) {
        $course = $courses[$i];
        $credit = isset($credits[$i]) ? intval($credits[$i]) : 3;
        $freq = isset($frequency[$i]) ? intval($frequency[$i]) : 2;
        
        // Add class sessions based on frequency
        for ($j = 0; $j < $freq; $j++) {
            $classesToSchedule[] = [
                'course' => $course,
                'credit' => $credit,
                'session' => $j + 1,
                'duration' => 1 // 1-hour class by default
            ];
        }
    }
    
    // Sort classes by credits (optional: this prioritizes more important classes)
    usort($classesToSchedule, function($a, $b) {
        return $b['credit'] - $a['credit']; // Sort by credit in descending order
    });
    
    // Shuffle the days to distribute classes more evenly
    $shuffledDays = $days;
    shuffle($shuffledDays);
    
    // If preferring morning classes, sort time slots from earliest to latest
    if ($preferMorning) {
        sort($timeSlots); // This should already be sorted, but making sure
    } else {
        // Random order for no specific preference
        $shuffledTimeSlots = $timeSlots;
        shuffle($shuffledTimeSlots);
        $timeSlots = $shuffledTimeSlots;
    }
    
    // Track classes per day to enforce maximum
    $classesPerDay = array_fill_keys($days, 0);
    
    // Track conflicts for reporting
    $schedulingConflicts = [];
    
    // Function to check if a slot is available
    function isSlotAvailable($day, $time, $conflicts, $timetable) {
        // Check if the slot is already taken
        if ($timetable[$day][$time] !== null) {
            return false;
        }
        
        // Check if the slot is in a conflict period
        if (isset($conflicts[$day])) {
            foreach ($conflicts[$day] as $conflict) {
                if ($time >= $conflict['start'] && $time < $conflict['end']) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    // Schedule classes
    foreach ($classesToSchedule as &$class) {
        $scheduled = false;
        
        // Try each day
        foreach ($shuffledDays as $day) {
            // Skip this day if we've reached max classes
            if ($classesPerDay[$day] >= $maxClassesPerDay) {
                continue;
            }
            
            // Try each time slot
            foreach ($timeSlots as $slot) {
                if (isSlotAvailable($day, $slot, $conflicts, $timetable)) {
                    // Schedule the class
                    $timetable[$day][$slot] = [
                        'course' => $class['course'],
                        'session' => $class['session']
                    ];
                    
                    $class['scheduled'] = true;
                    $class['day'] = $day;
                    $class['time'] = $slot;
                    
                    $classesPerDay[$day]++;
                    $scheduled = true;
                    break;
                }
            }
            
            if ($scheduled) {
                break;
            }
        }
        
        if (!$scheduled) {
            // Could not schedule this class
            $schedulingConflicts[] = $class;
        }
    }

    // Calculate statistics
    $totalScheduledClasses = 0;
    foreach ($days as $day) {
        $totalScheduledClasses += $classesPerDay[$day];
    }

    // Display the timetable
    echo '<div class="timetable-wrapper">';
    
    if (!empty($schedulingConflicts)) {
        echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4 dark:bg-yellow-800 dark:border-yellow-600 dark:text-yellow-200">
            <strong>Warning:</strong> Could not schedule ' . count($schedulingConflicts) . ' classes due to constraints. 
            Consider extending your available time or reducing conflicts.
        </div>';
    }
    
    echo '<table class="timetable-grid">
        <thead>
            <tr>
                <th class="py-2">Time / Day</th>';
                foreach ($days as $day) {
                    echo '<th class="py-2">' . $day . '</th>';
                }
            echo '</tr>
        </thead>
        <tbody>';
        
        foreach ($timeSlots as $slot) {
            echo '<tr>
                <td class="font-medium">' . $slot . '</td>';
            
            foreach ($days as $day) {
                if ($timetable[$day][$slot] !== null) {
                    $class = $timetable[$day][$slot];
                    $colorClass = $courseColors[$class['course']] ?? '';
                    
                    echo '<td class="' . $colorClass . ' p-2">
                        <div class="course-block">
                            <div class="font-bold">' . $class['course'] . '</div>
                            <div class="text-xs">Session ' . $class['session'] . '</div>
                        </div>
                    </td>';
                } else {
                    echo '<td></td>';
                }
            }
            
            echo '</tr>';
        }
        
        echo '</tbody>
    </table>';
    
    echo '<div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
        <p><strong>Total scheduled classes:</strong> ' . $totalScheduledClasses . ' out of ' . count($classesToSchedule) . '</p>
    </div>';
    
    echo '</div>';
} else {
    // Display placeholder or initial message
    echo '<div class="flex items-center justify-center h-64">
        <p class="text-gray-500 dark:text-gray-400 text-lg">Fill in your preferences and click "Generate Timetable" to create your schedule.</p>
    </div>';
}
?>