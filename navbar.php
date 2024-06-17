<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #ff4d4d !important; /* Lightened red for better readability */
        }
        .nav-link, .navbar-brand, .navbar-text {
            color: #ffffff !important;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            left: 0;
            top: calc(100% + 0.3125rem);
            background-color: #b33a3a; /* Darker red for dropdown background */
            z-index: 1;
            white-space: nowrap;
        }
        .dropdown:hover .dropdown-content, .dropdown-content:hover {
            display: block;
        }
        .dropdown-item {
            color: #ff6666 !important;
        }
        .dropdown-item:hover {
            background-color: #ff6666 !important; /* Slightly lighter red for hover effect */
            color: #ffffff !important;
        }
        .background-video video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .btn-primary {
            background-color: #3399cc !important; /* Blue for primary buttons */
            border-color: #3399cc !important;
        }
        .btn-primary:hover {
            background-color: #2277aa !important;
            border-color: #2277aa !important;
        }
        .btn-danger {
            background-color: #cc3333 !important; /* Darker red for danger buttons */
            border-color: #cc3333 !important;
        }
        .btn-danger:hover {
            background-color: #aa0000 !important;
            border-color: #aa0000 !important;
        }
    </style>
</head>
<body>
    <div class="background-video">
        <video autoplay muted loop>
            <source src="content/illustration.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="index.php">Valorant Fanpage</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="wikiDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Wiki
                    </a>
                    <div class="dropdown-menu" aria-labelledby="wikiDropdown">
                        <a class="dropdown-item" href="wiki.php#agents">Agents</a>
                        <a class="dropdown-item" href="wiki.php#weapons">Weapons</a>
                        <a class="dropdown-item" href="wiki.php#maps">Maps</a>
                        <a class="dropdown-item" href="wiki.php#skins">Skins</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="socialDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Social
                    </a>
                    <div class="dropdown-menu" aria-labelledby="socialDropdown">
                        <a class="dropdown-item" href="social.php#general">General Discussion</a>
                        <a class="dropdown-item" href="social.php#competitive">Competitive Play</a>
                        <a class="dropdown-item" href="social.php#lore">Lore & Story</a>
                        <a class="dropdown-item" href="social.php#creations">Community Creations</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="minigamesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Minigames
                    </a>
                    <div class="dropdown-menu" aria-labelledby="minigamesDropdown">
                        <a class="dropdown-item" href="minigames.php">Quiz Lodge</a>
                        <a class="dropdown-item" href="dailychallenge.php">Daily Quiz</a>
                        <a class="dropdown-item" href="aimtrainer.php">One Shot</a>
                        <a class="dropdown-item" href="freeplay.php">Free Play</a>
                        <a class="dropdown-item" href="leaderboard.php">Leaderboard</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="streams.php">Live Streams</a>
                </li>
                <?php
                $isLoggedIn = isset($_SESSION["username"]);
                if ($isLoggedIn && isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) :
                ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Admin Panel
                        </a>
                        <div class="dropdown-menu" aria-labelledby="adminDropdown">
                            <a class="dropdown-item" href="admin.php">Manage Users</a>
                            <a class="dropdown-item" href="gamedata.php">Manage Game Data</a>
                            <a class="dropdown-item" href="posts.php">Manage Posts</a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
            <?php if ($isLoggedIn) : ?>
                <span class="navbar-text mr-3">
                    <a href="profile.php" class="text-white text-decoration-none"><?php echo $_SESSION["username"]; ?></a>
                </span>
                <form class="form-inline" action="logout.php" method="post">
                    <button class="btn btn-danger" type="submit">Logout</button>
                </form>
            <?php else : ?>
                <a href="login.php" class="btn btn-primary text-decoration-none">Login</a>
            <?php endif; ?>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
