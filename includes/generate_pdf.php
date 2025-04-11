<?php
// PDF generation using DOMPDF
// First we need to check if we need to install the library
if (!file_exists('../dompdf-2.0.3/src/Dompdf.php')) {
    // Redirect to an error page or display a message
    echo "DOMPDF library not found. Please download it from https://github.com/dompdf/dompdf/releases";
    exit;
}

// Define our own minimal implementation of the missing HTML5 library
if (!class_exists('\\Masterminds\\HTML5')) {
    // Create a namespace for Masterminds if it doesn't exist
    if (!class_exists('\\Masterminds\\HTML5PlaceholderClass')) {
        // First, create a minimal HTML5 parser class
        class HTML5PlaceholderParser {
            public function parse($html) {
                // Use PHP's built-in DOM functions as a fallback
                $doc = new DOMDocument();
                $doc->loadHTML($html);
                return $doc;
            }
        }

        // Create a placeholder class with necessary methods
        class HTML5PlaceholderClass {
            private $options;
            
            public function __construct($options = []) {
                $this->options = $options;
            }
            
            public function loadHTML($html) {
                // Basic implementation that uses DOMDocument directly
                $doc = new DOMDocument('1.0', $this->options['encoding'] ?? 'UTF-8');
                // Suppress warnings during parsing
                @$doc->loadHTML($html);
                return $doc;
            }
            
            public function saveHTML($dom = null) {
                if ($dom instanceof DOMDocument) {
                    return $dom->saveHTML();
                } elseif ($dom instanceof DOMNode) {
                    return $dom->ownerDocument->saveHTML($dom);
                }
                return '';
            }
        }
    }
    
    // Register the class in the Masterminds namespace
    if (!class_exists('\\Masterminds\\HTML5')) {
        class_alias('HTML5PlaceholderClass', '\\Masterminds\\HTML5');
    }
}

// Include DOMPDF manually
require_once '../dompdf-2.0.3/src/Dompdf.php';
require_once '../dompdf-2.0.3/src/Options.php';
require_once '../dompdf-2.0.3/src/Helpers.php';
require_once '../dompdf-2.0.3/src/Frame.php';
require_once '../dompdf-2.0.3/src/FrameReflower/AbstractFrameReflower.php';
require_once '../dompdf-2.0.3/src/FrameDecorator/AbstractFrameDecorator.php';
require_once '../dompdf-2.0.3/lib/Cpdf.php';

// Also include our custom autoloader
if (file_exists('../dompdf-2.0.3/autoload.inc.php')) {
    require_once '../dompdf-2.0.3/autoload.inc.php';
}

// Reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Check if we have the timetable HTML
if (!isset($_POST['timetable_html']) || empty($_POST['timetable_html'])) {
    echo "No timetable data provided.";
    exit;
}

// Get the timetable HTML
$timetableHtml = $_POST['timetable_html'];

try {
    // Set up DOMPDF options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true); // We now have a basic HTML5 parser implementation
    $options->set('isRemoteEnabled', true); // To allow loading images from remote URLs
    $options->set('defaultFont', 'Arial');
    
    // Create new DOMPDF instance
    $dompdf = new Dompdf($options);
    
    // Prepare HTML for PDF - Use a simpler HTML structure to avoid parsing issues
    $html = '<html>
    <head>
        <meta charset="utf-8">
        <title>Timetable</title>
        <style>
            body { font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #333; }
            h1 { text-align: center; margin-bottom: 20px; color: #2563eb; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: center; }
            th { background-color: #f1f5f9; }
            .course-block { padding: 6px 3px; margin-bottom: 2px; }
            .course-color-1 { background-color: #dbeafe; }
            .course-color-2 { background-color: #d1fae5; }
            .course-color-3 { background-color: #fee2e2; }
            .course-color-4 { background-color: #ffedd5; }
            .course-color-5 { background-color: #ede9fe; }
            .course-color-6 { background-color: #e0e7ff; }
            .course-color-7 { background-color: #e0f2fe; }
            .course-color-8 { background-color: #fef3c7; }
        </style>
    </head>
    <body>
        <h1>Smart Timetable Generator</h1>
        
        ' . $timetableHtml . '
        
        <div style="text-align: center; font-size: 12px; color: #64748b; margin-top: 20px;">
            Generated on ' . date('F j, Y \a\t g:i a') . ' | Smart Timetable Generator
        </div>
    </body>
    </html>';
    
    // Load HTML into DOMPDF
    $dompdf->loadHtml($html);
    
    // Set paper size and orientation
    $dompdf->setPaper('A4', 'landscape');
    
    // Render the HTML as PDF
    $dompdf->render();
    
    // Output the generated PDF (inline or download)
    $dompdf->stream("timetable.pdf", array("Attachment" => false));
    
} catch (Exception $e) {
    // Display any errors that occur during PDF generation
    echo "<h1>Error generating PDF</h1>";
    echo "<p>Error message: " . $e->getMessage() . "</p>";
    echo "<p>If you're seeing this error, you might need to install the full DOMPDF library with all its dependencies using Composer.</p>";
    echo "<p><a href='javascript:history.back()'>Go back to the timetable</a></p>";
}

// Exit to prevent additional output
exit(0);
?>