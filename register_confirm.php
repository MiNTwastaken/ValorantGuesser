<!DOCTYPE html>
<html>
<head>
    <title>Confirm Registration</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
<div class="navbar">
    <div class="container">
        <a href="index.php">Valorant Fanpage</a>
        <nav>
            <div class="dropdown">
                <a href="wiki.php" class="dropdown-btn">Wiki</a>
                <div class="dropdown-content">
                    <a href="wiki.php#agents">Agents</a>
                    <a href="wiki.php#weapons">Weapons</a>
                    <a href="wiki.php#maps">Maps</a>
                    <a href="wiki.php#strategies">Strategies</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="forum.php" class="dropdown-btn">Forum</a>
                <div class="dropdown-content">
                    <a href="forum.php#general">General Discussion</a>
                    <a href="forum.php#competitive">Competitive Play</a>
                    <a href="forum.php#lore">Lore & Story</a>
                    <a href="forum.php#creations">Community Creations</a>
                </div>
            </div>
            
            <div class="dropdown">
                <a href="minigames.php" class="dropdown-btn">Minigames</a>
                <div class="dropdown-content">
                    <a href="minigames.php#daily">Daily Quiz</a>
                    <a href="minigames.php#oneshot">One Shot</a>
                    <a href="minigames.php#freeplay">Free Play</a>
                </div>
            </div>

            <?php
            session_start();
            $isLoggedIn = isset($_SESSION["username"]);
            ?>

            <?php if ($isLoggedIn && isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) : ?>
                <div class="dropdown">
                    <a href="admin.php" class="dropdown-btn">Admin Panel</a>
                    <div class="dropdown-content">
                        <a href="admin.php#users">Manage Users</a> 
                    </div>
                </div>
            <?php endif; ?>


            <?php if ($isLoggedIn) : ?>
                <div class="logged-in-user">
                    <a href="profile.php" class="profile-link"><?php echo $_SESSION["username"]; ?></a>
                    <form action="logout.php" method="post">
                        <button type="submit">Logout</button>
                    </form>
                </div>
            <?php else : ?>
                <a href="login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</div>
    <div class="content">
        <h2>Confirm Registration</h2>
    
        <?php
        // Database Connection
        $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
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
