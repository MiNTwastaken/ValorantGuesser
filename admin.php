<!DOCTYPE html>
<html>
<head>
    <title>Valorant Guesser Admin Panel</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
    <?php
    session_start();

    // Security Check
    if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== 1) {
        header("Location: login.php");
        exit; 
    }

    // Database Connection
    $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Handle User Management Actions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["make_admin"])) {
            // ... (make admin logic) ...
        } elseif (isset($_POST["delete_user"])) {
            // ... (delete user logic) ...
        }
    }

    // Fetch User Data for Display
    $sql = "SELECT username, email, picture, favorite, lvl, exp, admin FROM user";
    $result = mysqli_query($connection, $sql);

    $users = [];
    if (mysqli_num_rows($result) > 0) {
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    ?>

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
    <div class="admin-panel">
    <h1>Valorant Guesser Admin Panel</h1>
    <p>Welcome, <?php echo $_SESSION["username"];?>. Manage everything here</p>

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
                            <form method="post" action="">
                                <input type="hidden" name="make_admin" value="<?php echo $user['username']; ?>">
                                <button type="submit" class="make-admin-btn">Make Admin</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="">
                            <input type="hidden" name="delete_user" value="<?php echo $user['username']; ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
