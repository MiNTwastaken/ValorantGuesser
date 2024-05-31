<?php
session_start();

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch leaderboard data
$sql = "SELECT u.*, l.name AS level_name, l.total_exp AS next_level_exp, 
       RANK() OVER (ORDER BY u.lvl DESC, u.exp DESC) AS rank
       FROM user u 
       JOIN levels l ON u.lvl = l.id";

$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Check if the user is logged in and find their rank
$isLoggedIn = isset($_SESSION["username"]);
$currentUserRank = null;
$currentUserLevel = null;
$currentUserExp = null;

if ($isLoggedIn) {
    $rankSql = "SELECT u.*, l.name AS level_name, l.total_exp AS next_level_exp,
                RANK() OVER (ORDER BY u.lvl DESC, u.exp DESC) AS rank
                FROM user u
                JOIN levels l ON u.lvl = l.id
                WHERE username = ?";
    $rankStmt = mysqli_prepare($connection, $rankSql);
    mysqli_stmt_bind_param($rankStmt, "s", $_SESSION["username"]);
    mysqli_stmt_execute($rankStmt);
    $rankResult = mysqli_stmt_get_result($rankStmt);

    if ($rankResult && mysqli_num_rows($rankResult) == 1) {
        $rankRow = mysqli_fetch_assoc($rankResult);
        $currentUserRank = $rankRow['rank'];
        $currentUserLevel = $rankRow['level_name'];
        $currentUserExp = $rankRow['exp'] . '/' . $rankRow['next_level_exp'];
    }

    mysqli_stmt_close($rankStmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Valorant Forum - Leaderboard</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #fff;
        }

        .leaderboard-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #202124;
            border-radius: 10px;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leaderboard-table th, .leaderboard-table td {
            border: 1px solid #303134;
            padding: 10px;
            text-align: center;
        }

        .leaderboard-table th {
            background-color: #303134;
        }

        .leaderboard-table tr:nth-child(even) {
            background-color: #28292C;
        }

        .rank {
            color: #ff4500;
            font-weight: bold;
        }

        h2 { 
            color: #fff;
            font-size: 2em;
            text-align: center;
        }

        .loginn-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff4500;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .current-user {
            background-color: #38393c; /* Slightly lighter background for current user */
        }
    </style>
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
    <div class="leaderboard-container">
        <?php if ($isLoggedIn && $currentUserRank !== null): ?>
            <div style="text-align: center; margin-bottom: 20px;">
                <h2>Your Rank</h2>
                <p><strong><?php echo $currentUserRank; ?></strong> - <?php echo $currentUserLevel; ?> (<?php echo $currentUserExp; ?> EXP)</p>
            </div>
        <?php endif; ?>

        <h2>Leaderboard</h2> 
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Username</th>
                    <th>Level</th>
                    <th>EXP</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr class="<?php echo ($isLoggedIn && $row['username'] == $_SESSION["username"]) ? 'current-user' : ''; ?>">
                        <td class="rank"><?php echo $row['rank']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['level_name']); ?></td>
                        <td><?php echo $row['exp'] . '/' . $row['next_level_exp']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div style="text-align: center; margin-top: 20px;"> 
            <?php if (!$isLoggedIn) : ?>
                <a href="login.php" class="loginn-btn">Login to See Your Rank</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
