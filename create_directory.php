<?php
// Define the directory path (relative to this script)
$directory = 'my_new_directory';

// Set permissions (0755 = owner has full access, others can read/execute)
$permissions = 0755;

// Check if directory already exists
if (!file_exists($directory)) {
    // Attempt to create directory
    if (mkdir($directory, $permissions, true)) { // 'true' enables recursive creation
        echo "Success! Directory '$directory' was created.";

        // Optional: Create an index.html to prevent directory listing
        file_put_contents("$directory/index.html", "<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden</h1></body></html>");
    } else {
        echo "Error: Failed to create directory '$directory'.";
    }
} else {
    echo "Notice: Directory '$directory' already exists.";
}

// Show the absolute path where directory was created
echo "<br>Directory location: " . realpath($directory);
?>