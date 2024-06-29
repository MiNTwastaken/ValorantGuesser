<?php
require 'vendor/autoload.php'; // Include PHPMailer files

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['new_email']) && isset($_GET['username']) && isset($_GET['old_email'])) {
    $newEmail = mysqli_real_escape_string($connection, $_GET['new_email']);
    $username = mysqli_real_escape_string($connection, $_GET['username']);
    $oldEmail = mysqli_real_escape_string($connection, $_GET['old_email']);

    // Update email
    $sql = "UPDATE user SET email = ? WHERE username = ? AND email = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $newEmail, $username, $oldEmail);

    if (mysqli_stmt_execute($stmt)) {
        echo "Email updated successfully.";
    } else {
        echo "Error updating email: " . mysqli_error($connection);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request.";
}

mysqli_close($connection);
?>
