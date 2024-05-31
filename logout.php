<?php
// Start session (if not already started)
session_start();

// Destroy the user session
session_destroy();

// Redirect user to the main  page after logout
header("Location: index.php");  // Replace with your main page URL if different
exit;
?>