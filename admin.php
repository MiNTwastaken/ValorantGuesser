<!DOCTYPE html>
<html>
<head>
    <title>Valorant Fanpage Admin Panel</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
    <script src="script.js"></script>
    <?php
    include 'navbar.php';
    // Security Check
    if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== 1) {
        header("Location: login.php");
        exit;
    }

    // Database Connection
    $connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Handle User Management Actions and Set Session Messages
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["make_admin"])) {
            $username = $_POST["make_admin"];
            $sql = "UPDATE user SET admin = 1 WHERE username = '$username'";
            if (mysqli_query($connection, $sql)) {
                $_SESSION['success_message'] = "User '$username' has been made an admin.";
            } else {
                $_SESSION['error_message'] = "Error making user admin: " . mysqli_error($connection);
            }
        } elseif (isset($_POST["delete_user"])) {
            $username = $_POST["delete_user"];
            $sql = "DELETE FROM user WHERE username = '$username'";
            if (mysqli_query($connection, $sql)) {
                $_SESSION['success_message'] = "User '$username' has been deleted.";
            } else {
                $_SESSION['error_message'] = "Error deleting user: " . mysqli_error($connection);
            }
        }

        // Redirect to avoid duplicate messages on refresh
        header("Location: admin.php");
        exit;
    }

    // Fetch User Data for Display
    $sql = "SELECT username, email, picture, favorite, lvl, exp, admin FROM user";
    $result = mysqli_query($connection, $sql);

    $users = [];
    if (mysqli_num_rows($result) > 0) {
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    ?>

    <div class="admin-panel">
        <h1>Valorant Guesser Admin Panel</h1>
        <p>Welcome, <?php echo $_SESSION["username"]; ?>. Manage everything here</p>

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

        <form method="post" action="show_data.php">
            <select name="data_type">
                <option value="ability">Abilities</option>
                <option value="agent">Agents</option>
                <option value="graffiti">Graffiti</option>
                <option value="playercard">Player Cards</option>
                <option value="quote">Quotes</option>
                <option value="weapon">Weapons</option>
            </select>
            <button type="submit" class="show-data-btn">Show Data</button>
        </form>

        <a href="gamedata.php" class="view-gamedata-btn">Fetch information from API</a>
        <a href="posts.php" class="view-posts-btn">Manage posts</a>

        <h2>User Management</h2>
        <div class="user-list">
            <?php foreach ($users as $user) : ?>
                <div class="user-box">
                    <div class="user-info">
                        <img src="<?php echo $user['picture']; ?>" alt="Profile Picture" class="user-picture">
                        <div class="user-details">
                            <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                            <p><strong>Favorite:</strong> <?php echo $user['favorite']; ?></p>
                            <p><strong>Level:</strong> <?php echo $user['lvl']; ?></p>
                            <p><strong>Experience:</strong> <?php echo $user['exp']; ?></p>
                            <p><strong>Admin:</strong> <?php echo $user['admin'] ? 'Yes' : 'No'; ?></p>
                        </div>
                    </div>
                    <div class="user-actions">
                        <?php if (!$user['admin']) : ?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="make_admin" value="<?php echo $user['username']; ?>">
                                <button type="submit" class="make-admin-btn">Make Admin</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="delete_user" value="<?php echo $user['username']; ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> 
    </div>
    <script>
        <?php 
        if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])) {
            if (isset($_SESSION['success_message'])) {
                $message = $_SESSION['success_message'];
            } else {
                $message = $_SESSION['error_message'];
            }
            echo "alert('$message');";
        }
        ?>
    </script>
</body>
</html>
