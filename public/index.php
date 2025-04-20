<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if PHP is working
echo "<!-- PHP is working -->";

try {
    // Include routes configuration
    require_once __DIR__ . '/../routes/routes.php';
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>An error occurred: " . $e->getMessage() . "</p>";
    echo "<p>Please check server logs for more information.</p>";
} 