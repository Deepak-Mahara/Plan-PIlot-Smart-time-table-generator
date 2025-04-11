<?php
// Gemini API Key Validation Script
// This simple script tests if your Gemini API key is working correctly

// Include the environment variable loader
require_once 'includes/env_loader.php';

// Get the API key from the environment
$apiKey = getenv('GEMINI_API_KEY') ?: '';

// Display status header
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini API Key Validation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dark mode configuration -->
    <script>
        if (localStorage.theme === "dark" || 
            (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-6">
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Gemini API Key Validation</h1>';

// Check if the key exists
if (empty($apiKey)) {
    echo '<div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6">
            <p class="font-bold">API Key Error</p>
            <p>No API key found. Please make sure you have set the GEMINI_API_KEY in your .env file.</p>
          </div>';
    
    echo '<div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-md mb-6">
            <h2 class="text-lg font-bold text-yellow-800 dark:text-yellow-200 mb-2">How to fix this:</h2>
            <ol class="list-decimal ml-5 text-yellow-800 dark:text-yellow-200 space-y-1">
                <li>Check if the .env file exists in your project root directory</li>
                <li>Open the .env file and make sure it contains a line with GEMINI_API_KEY=your_api_key</li>
                <li>Make sure there are no extra spaces around the API key</li>
                <li>Restart your web server to ensure the environment variables are reloaded</li>
            </ol>
          </div>';
} else {
    // Key exists, let's test it with a simple request
    $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey;
    
    // Simple test prompt
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Say "Hello World" to validate this API connection.']
                ]
            ]
        ]
    ];
    
    // Initialize cURL session
    $ch = curl_init($apiUrl);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 15 second timeout
    
    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $errorNo = curl_errno($ch);
    
    curl_close($ch);
    
    // Process the response
    $isValid = false;
    
    if ($errorNo) {
        // cURL error
        echo '<div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6">
                <p class="font-bold">Connection Error</p>
                <p>Failed to connect to the Gemini API: ' . htmlspecialchars($error) . '</p>
              </div>';
    } elseif ($httpCode != 200) {
        // HTTP error
        $responseData = json_decode($response, true);
        $errorMessage = isset($responseData['error']['message']) ? $responseData['error']['message'] : 'Unknown error';
        
        echo '<div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6">
                <p class="font-bold">API Error (HTTP ' . $httpCode . ')</p>
                <p>' . htmlspecialchars($errorMessage) . '</p>
              </div>';
                
        if (strpos($errorMessage, 'API key not valid') !== false || $httpCode == 401) {
            echo '<div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-md mb-6">
                    <h2 class="text-lg font-bold text-yellow-800 dark:text-yellow-200 mb-2">Invalid API Key:</h2>
                    <ol class="list-decimal ml-5 text-yellow-800 dark:text-yellow-200 space-y-1">
                        <li>Your API key appears to be invalid or has expired</li>
                        <li>Go to <a href="https://makersuite.google.com/app/apikey" class="underline" target="_blank">Google AI Studio</a> to create a new key</li>
                        <li>Make sure you have enabled the Gemini API for your Google Cloud project</li>
                        <li>Check if you\'ve reached your quota limit or if billing is properly set up</li>
                    </ol>
                  </div>';
        }
    } else {
        // Successful response
        $responseData = json_decode($response, true);
        
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $isValid = true;
            $aiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
            
            echo '<div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 mb-6">
                    <p class="font-bold">Success!</p>
                    <p>Your Gemini API key is valid and working correctly.</p>
                  </div>';
                  
            echo '<div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-md mb-6">
                    <h2 class="text-lg font-bold text-blue-800 dark:text-blue-200 mb-2">API Response:</h2>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded border border-blue-200 dark:border-blue-700">
                        ' . nl2br(htmlspecialchars($aiResponse)) . '
                    </div>
                  </div>';
        } else {
            echo '<div class="bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-300 p-4 mb-6">
                    <p class="font-bold">Unexpected Response</p>
                    <p>The API key appears valid, but the response format was unexpected.</p>
                  </div>';
                  
            echo '<div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-md mb-6">
                    <h2 class="text-lg font-bold mb-2">Raw Response:</h2>
                    <pre class="bg-white dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-700 overflow-x-auto text-sm">
                        ' . htmlspecialchars(json_encode($responseData, JSON_PRETTY_PRINT)) . '
                    </pre>
                  </div>';
        }
    }
    
    // Display the masked API key (with middle portion hidden)
    $maskedKey = '';
    if (strlen($apiKey) > 8) {
        $maskedKey = substr($apiKey, 0, 4) . str_repeat('*', strlen($apiKey) - 8) . substr($apiKey, -4);
    } else {
        $maskedKey = '****';
    }
    
    echo '<div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-md mb-6">
            <h2 class="text-lg font-bold mb-2">API Key Information:</h2>
            <p><strong>Key:</strong> ' . $maskedKey . '</p>
            <p><strong>Length:</strong> ' . strlen($apiKey) . ' characters</p>
            <p><strong>Status:</strong> ' . ($isValid ? '<span class="text-green-600 dark:text-green-400">Valid</span>' : '<span class="text-red-600 dark:text-red-400">Invalid or Error</span>') . '</p>
          </div>';
}

// Display navigation links
echo '<div class="mt-6 flex gap-4">
        <a href="index.php" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Back to Main Page</a>
        <a href="ai_timetable.php" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded">Go to AI Timetable</a>
      </div>';

echo '</div>
    <footer class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
        <p>Smart Timetable Generator - Gemini API Validation Tool</p>
    </footer>
</body>
</html>';
?>