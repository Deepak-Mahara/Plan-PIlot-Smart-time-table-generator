<?php
// Start session to access timetable data
session_start();

// Include the DOMPDF library
require_once '../dompdf-2.0.3/autoload.inc.php';

// Add the Cpdf class to the Dompdf namespace
class_alias('\Cpdf', '\Dompdf\Cpdf');

// Reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Get timetable data from session
$timetable = isset($_SESSION['timetable_data']) ? $_SESSION['timetable_data'] : [];
$aiResponse = isset($_SESSION['ai_response']) ? $_SESSION['ai_response'] : [];

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Initialize dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);

// Schedule insights from prompt
$insights = [
    "Early Morning Focus: The schedule leverages the student's peak productivity time (early morning) by scheduling the most demanding courses, AI and Frontend, during this period. This maximizes cognitive resources for these subjects.",
    "Balanced Course Load: The schedule distributes the course load evenly across the week, preventing overwhelming concentration on any single day, thereby improving the student's ability to focus and learn.",
    "Strategic Course Placement: More demanding courses (AI) are scheduled before less demanding ones (Frontend) to ensure optimum cognitive energy usage.",
    "Incorporation of Breaks: Sufficient breaks and a dedicated lunch hour are included to allow for rest and digestion, which contributes to better concentration and information retention."
];

// Generate HTML for the PDF
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Timetable</title>
    <style>
        body {
            font-family: "DejaVu Sans", "Arial", sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #2563eb;
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }
        h2 {
            color: #1e40af;
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f7ff;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        td {
            padding: 10px;
            vertical-align: top;
        }
        .course-block {
            background-color: #e6f0ff;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        .course-name {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .course-details {
            font-size: 10px;
            color: #666;
        }
        .insights {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        .insights h2 {
            margin-top: 0;
        }
        .insights ul {
            padding-left: 20px;
        }
        .insights li {
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PlanPilot Academic Timetable</h1>
        <div class="header-info">
            <p>Generated on: ' . date('F j, Y, g:i a') . '</p>
        </div>';

// Extract timetable data
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$allTimeSlots = [];
$timetableData = [];

// Sample data structure - either use session data or fallback to sample
if (isset($_SESSION['ai_timetable']) && is_array($_SESSION['ai_timetable'])) {
    $timetableData = $_SESSION['ai_timetable'];
} else {
    // Sample timetable
    $timetableData = [
        'Monday' => [
            '08:00 - 09:30' => ['course' => 'Artificial Intelligence', 'instructor' => 'Dr. Smith', 'location' => 'Room 201'],
            '10:00 - 11:30' => ['course' => 'Frontend Development', 'instructor' => 'Prof. Johnson', 'location' => 'Lab 3'],
            '12:00 - 13:00' => ['course' => 'Lunch Break', 'notes' => 'Rest and recharge'],
            '13:30 - 15:00' => ['course' => 'Database Systems', 'instructor' => 'Dr. Davis', 'location' => 'Room 105']
        ],
        'Tuesday' => [
            '08:00 - 09:30' => ['course' => 'Machine Learning', 'instructor' => 'Dr. Wilson', 'location' => 'Room 302'],
            '10:00 - 11:30' => ['course' => 'Software Engineering', 'instructor' => 'Prof. Miller', 'location' => 'Room 201'],
            '12:00 - 13:00' => ['course' => 'Lunch Break', 'notes' => 'Rest and recharge'],
            '13:30 - 15:00' => ['course' => 'Research Methodology', 'instructor' => 'Dr. Brown', 'location' => 'Library']
        ],
        'Wednesday' => [
            '08:00 - 09:30' => ['course' => 'Artificial Intelligence', 'instructor' => 'Dr. Smith', 'location' => 'Room 201'],
            '10:00 - 11:30' => ['course' => 'Frontend Development', 'instructor' => 'Prof. Johnson', 'location' => 'Lab 3'],
            '12:00 - 13:00' => ['course' => 'Lunch Break', 'notes' => 'Rest and recharge'],
            '13:30 - 15:00' => ['course' => 'Database Systems', 'instructor' => 'Dr. Davis', 'location' => 'Room 105']
        ],
        'Thursday' => [
            '08:00 - 09:30' => ['course' => 'Machine Learning', 'instructor' => 'Dr. Wilson', 'location' => 'Room 302'],
            '10:00 - 11:30' => ['course' => 'Software Engineering', 'instructor' => 'Prof. Miller', 'location' => 'Room 201'],
            '12:00 - 13:00' => ['course' => 'Lunch Break', 'notes' => 'Rest and recharge'],
            '13:30 - 15:00' => ['course' => 'Research Methodology', 'instructor' => 'Dr. Brown', 'location' => 'Library']
        ],
        'Friday' => [
            '08:00 - 09:30' => ['course' => 'Project Work', 'instructor' => 'Various Professors', 'location' => 'Lab 5'],
            '10:00 - 11:30' => ['course' => 'Technical Communication', 'instructor' => 'Prof. Taylor', 'location' => 'Room 101'],
            '12:00 - 13:00' => ['course' => 'Lunch Break', 'notes' => 'Rest and recharge'],
            '13:30 - 15:00' => ['course' => 'Free Study Period', 'notes' => 'Personal revision and assignments']
        ]
    ];
}

// Get all time slots
foreach ($timetableData as $day => $slots) {
    if (is_array($slots)) {
        foreach ($slots as $time => $details) {
            $allTimeSlots[$time] = true;
        }
    }
}

// Sort time slots
ksort($allTimeSlots);
$timeSlots = array_keys($allTimeSlots);

// Generate timetable HTML
$html .= '<table>';
$html .= '<tr>
    <th>Time</th>
    <th>Monday</th>
    <th>Tuesday</th>
    <th>Wednesday</th>
    <th>Thursday</th>
    <th>Friday</th>
</tr>';

foreach ($timeSlots as $time) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($time) . '</td>';
    
    foreach ($days as $day) {
        $html .= '<td>';
        if (isset($timetableData[$day][$time])) {
            $courseDetails = $timetableData[$day][$time];
            if (is_array($courseDetails) && isset($courseDetails['course'])) {
                $courseName = htmlspecialchars($courseDetails['course']);
                $instructor = isset($courseDetails['instructor']) ? htmlspecialchars($courseDetails['instructor']) : '';
                $location = isset($courseDetails['location']) ? htmlspecialchars($courseDetails['location']) : '';
                $notes = isset($courseDetails['notes']) ? htmlspecialchars($courseDetails['notes']) : '';
                
                $html .= '<div class="course-block">';
                $html .= '<div class="course-name">' . $courseName . '</div>';
                
                if ($instructor || $location) {
                    $html .= '<div class="course-details">';
                    if ($instructor) {
                        $html .= $instructor;
                        if ($location) {
                            $html .= ' • ' . $location;
                        }
                    } elseif ($location) {
                        $html .= $location;
                    }
                    $html .= '</div>';
                }
                
                if ($notes) {
                    $html .= '<div class="course-details">Note: ' . $notes . '</div>';
                }
                
                $html .= '</div>';
            }
        }
        $html .= '</td>';
    }
    
    $html .= '</tr>';
}

$html .= '</table>';

// Add insights section
$html .= '<div class="insights">
    <h2>Schedule Insights</h2>
    <ul>';

foreach ($insights as $insight) {
    $html .= '<li>' . htmlspecialchars($insight) . '</li>';
}

$html .= '</ul>
</div>';

// Add footer
$html .= '<div class="footer">
    <p>PlanPilot Timetable Planning System • Generated on ' . date('F j, Y, g:i a') . '</p>
</div>';

$html .= '</div>
</body>
</html>';

// Load HTML to dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF
$dompdf->stream("academic_timetable.pdf", ["Attachment" => false]);
exit();
?>