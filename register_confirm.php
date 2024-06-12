<!DOCTYPE html>
<html>
<head>
    <title>Confirm Registration</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
    <script src="script.js"></script>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <h2>Confirm Registration</h2>
    
        <?php
        // Database Connection
        $connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["email"])) {
            $email = $_GET["email"];

            // Update email_confirm status in the database
            $sql = "UPDATE user SET email_confirmed = 1 WHERE email = '$email'";

            if (mysqli_query($connection, $sql)) {
                $_SESSION['success_message'] = "Your account has been activated. You will be redirected to the login page shortly.";
                header("Refresh: 5; url=login.php"); 
            } else {
                $_SESSION['error_message'] = "Error activating your account: " . mysqli_error($connection);
            }
        }
        ?>
        
        <?php
        // Display session messages
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Clear the message
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Clear the message
        }
        ?>
    </div>
</body>
</html>
