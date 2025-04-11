<?php
/**
 * Custom autoloader for DOMPDF without Composer
 */

// Register autoloader
spl_autoload_register(function($class) {
    // DOMPDF namespace prefix
    $prefix = 'Dompdf\\';
    
    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';
    
    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Convert namespace part to a path
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Define the legacy Dompdf class for any code that might still use it
if (!class_exists('DOMPDF')) {
    class DOMPDF extends Dompdf\Dompdf {}
}
?>