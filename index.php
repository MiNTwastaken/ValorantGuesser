<!DOCTYPE html>
<html>
<head>
    <title>Valorant Fanpage</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
<script src="script.js"></script>
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

                <?php
                session_start();
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

    <div class="content">
        <section class="hero"> 
            <h2>Welcome to the Ultimate Valorant Fan Hub!</h2>
            <p>Ignite your passion for Valorant in our vibrant community! Dive into the in-depth knowledge of our Wiki, strategize with fellow players on the social, and test your skills with our thrilling Minigames.</p>
        </section>

        <section class="features">
            <h3>What Awaits You?</h3>
            <ul>
                <li><strong>Master the Game:</strong> Our Wiki is your go-to resource for agent guides, weapon stats, map breakdowns, and advanced strategies.</li>
                <li><strong>Connect and Discuss:</strong> Join the conversation in our social, share your experiences, debate the meta, and forge friendships with other Valorant enthusiasts.</li>
                <li><strong>Challenge Yourself:</strong> Put your knowledge to the test with our Daily Quizzes, Test your aim at the Aim Trainer Minigame, and guess countless things Free Play.</li>
            </ul>
        </section>

        <section class="call-to-action">
            <h3>Ready to Join the Fun?</h3>
            <p>Don't miss out on the action! Create your account today and unlock a world of Valorant knowledge, camaraderie, and excitement.</p>
            <?php if (!$isLoggedIn) : ?>
                <a href="register.php" class="login-btn">Register Now</a> 
            <?php endif; ?>
        </section>
    </div>
</body>
</html>