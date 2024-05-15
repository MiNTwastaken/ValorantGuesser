<!DOCTYPE html>
<html>
<head>
  <title>Valorant Wiki</title>
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
    <h1>Valorant Wiki</h1>

    <p>Welcome to the Valorant Wiki! Here you'll find comprehensive information about the game.</p>

    <h2>Agents</h2>
    <div id="agents-container">
      </div>

    <h2>Weapons</h2>
    <div id="weapons-container">
      </div>

    <h2>Maps</h2>
    <div id="maps-container">
      </div>

    <h2>Strategies</h2>
    <p>This section will cover various strategies for different aspects of the game, including:</p>
    <ul>
      <li>Attack and Defense Strategies</li>
      <li>Agent Compositions</li>
      <li>Economy Management</li>
      <li>Aiming Techniques</li>
    </ul>

    </div>

  <script src="script.js"></script>
</body>
</html>
