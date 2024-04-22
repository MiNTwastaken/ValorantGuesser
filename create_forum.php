<?php
// Start session (if not already started)
session_start();

// Check if user is logged in (optional: redirect if not)
if (!isset($_SESSION["username"])) {
  // Redirect to login page or display error message
  header("Location: login.php"); // Replace with your login page URL
  exit;
}

// Database Connection (replace with your connection details)
$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = mysqli_real_escape_string($connection, $_POST["title"]); // Sanitize input
  $content = mysqli_real_escape_string($connection, $_POST["content"]); // Sanitize input
  $createdBy = $_SESSION["username"]; // Get username from session

  // Insert post into database
  $sql = "INSERT INTO posts (title, content, created_by) VALUES ('$title', '$content', '$createdBy')";
  if (mysqli_query($connection, $sql)) {
    echo "post created successfully!";
    // Optionally, redirect to post list or the newly created post page
  } else {
    echo "Error creating post: " . mysqli_error($connection);
  }
}

// Close database connection
mysqli_close($connection);
?>
