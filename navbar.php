<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="background-video">
    <video autoplay muted loop>
        <source src="content/illustration.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
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
                    <a href="wiki.php#skins">Skins</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="social.php" class="dropdown-btn">Social</a>
                <div class="dropdown-content">
                    <a href="social.php#general">General Discussion</a>
                    <a href="social.php#competitive">Competitive Play</a>
                    <a href="social.php#lore">Lore & Story</a>
                    <a href="social.php#creations">Community Creations</a>
                </div>
            </div>
            
            <div class="dropdown">
                <a href="minigames.php" class="dropdown-btn">Minigames</a>
                <div class="dropdown-content">
                    <a href="dailychallenge.php">Daily Quiz</a>
                    <a href="aimtrainer.php">One Shot</a>
                    <a href="freeplay.php">Free Play</a>
                    <a href="leaderboard.php">Leaderboard</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="streams.php" class="dropdown-btn">Live Streams</a>
            </div>
            <?php
            $isLoggedIn = isset($_SESSION["username"]);
            ?>

            <?php if ($isLoggedIn && isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) : ?>
                <div class="dropdown">
                    <a href="admin.php" class="dropdown-btn">Admin Panel</a>
                    <div class="dropdown-content">
                        <a href="admin.php">Manage Users</a>
                        <a href="gamedata.php">Manage Game Data</a>
                        <a href="posts.php">Manage Posts</a>
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