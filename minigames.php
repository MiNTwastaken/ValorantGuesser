<!DOCTYPE html>
<html>
<head>
  <title>Valorant Minigames</title>
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
    <h1>Valorant Minigames</h1>
    <p>Test your Valorant skills and knowledge with these fun minigames!</p>

    <div class="minigame-grid">
      <div class="minigame">
        <h2>Daily Quiz</h2>
        <p>Answer questions about Valorant lore, agents, and gameplay to test your knowledge.</p>
        <a href="oneshot.php">Play Daily Quiz</a>
      </div>
      <div class="minigame">
        <h2>One Shot</h2>
        <p>Sharpen your aim and reflexes by eliminating targets within a time limit.</p>
        <a href="aimtrainer.php">Play One Shot</a>
      </div>
      <div class="minigame">
        <h2>Free Play</h2>
        <p>Practice your shooting skills and explore maps in a relaxed environment.</p>
        <a href="freeplay.php">Play Free Play</a>
      </div>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>