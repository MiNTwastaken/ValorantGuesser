<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aim Trainer</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
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
    <h1>Aim Trainer</h1>
    <div id="game-container">
        <div id="target"></div>
    </div>
    <div id="scoreboard">0</div>
    <button id="start-button">Start Game</button>
    <script>
        const target = document.getElementById('target');
        const scoreboard = document.getElementById('scoreboard');
        const startButton = document.getElementById('start-button');
        let score = 0;
        let startTime; 
        let targetCount = 30; 

        target.addEventListener('click', () => {
        score++;
        scoreboard.textContent = score;
        resetTarget();
        if (score >= targetCount) {
            gameOver();
        }
        });

        function resetTarget() {
        let topPosition = Math.random() * (100 - target.clientHeight) + '%';
        let leftPosition = Math.random() * (100 - target.clientWidth) + '%';
        target.style.top = topPosition;
        target.style.left = leftPosition;
        target.style.backgroundColor = 'red';
        }

        function gameOver() {
        const endTime = Date.now();
        if (startTime) {
            const totalTime = (endTime - startTime) / 1000; 
            alert(`You hit ${score} targets in ${totalTime.toFixed(2)} seconds!`);
        } else {
            alert("Game hasn't started yet!"); 
        }
        score = 0;
        startButton.disabled = false; 
        }

        function startGame() {
        startTime = Date.now(); 
        score = 0;
        scoreboard.textContent = score; 
        resetTarget();
        startButton.disabled = true;
        }

        startButton.addEventListener('click', startGame);

        document.addEventListener('click', (event) => {
        if (event.target !== startButton) {
            target.style.backgroundColor = 'lightgray';
            event.preventDefault();
        }
        });
    </script>
</body>
</html>
