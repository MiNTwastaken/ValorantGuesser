<?php
session_start();

$uuid = $_GET['uuid'];
$type = $_GET['type'];

// Corrected API endpoint for agents
$apiUrl = "https://valorant-api.com/v1/{$type}s/{$uuid}"; 

$apiResponse = @file_get_contents($apiUrl); // Suppress errors for now

if ($apiResponse === false) {
    echo "<p>Error: Unable to fetch data from the API. Please try again later.</p>";
    exit;
}

$itemData = json_decode($apiResponse, true);

// Check for API errors
if (isset($itemData['status']) && $itemData['status'] != 200) { 
    echo "<p>Error: {$itemData['status']} - {$itemData['error']}</p>";
    exit;
}

$itemData = $itemData['data']; // Extract the actual item data
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $itemData['displayName']; ?> Details</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        .content {
            padding: 20px;
        }

        .content h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .item-details img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
        }

        .item-details ul {
            list-style: disc;
            padding-left: 20px;
        }
        
        .item-details li {
            margin-bottom: 5px;
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

    <div class="content item-details">
        <h1><?php echo $itemData['displayName']; ?></h1>
        
        <img src="<?php echo $itemData['displayIcon']; ?>" alt="<?php echo $itemData['displayName']; ?>">

        <p><?php echo $itemData['description']; ?></p>

        <?php
        switch ($type) {
            case 'agent':
                echo "<p>Role: {$itemData['role']['displayName']}</p>";
                echo "<h2>Abilities:</h2>";
                echo "<ul>";
                foreach ($itemData['abilities'] as $ability) {
                    if ($ability['slot'] !== "Passive") {
                        echo "<li><strong>{$ability['displayName']}</strong>: {$ability['description']}</li>";
                    }
                }
                echo "</ul>";
                break;
            case 'weapon':
                echo "<h2>Weapon Stats:</h2>";
                echo "<ul>";
                echo "<li>Fire Rate: {$itemData['weaponStats']['fireRate']}</li>";
                echo "<li>Magazine Size: {$itemData['weaponStats']['magazineSize']}</li>";
                echo "<li>Cost: {$itemData['shopData']['cost']}</li>";
                echo "</ul>";
                // ... (Display skin levels if applicable)
                break;
            case 'map':
                echo "<h2>Callouts:</h2>";
                echo "<ul>";
                foreach ($itemData['callouts'] as $callout) {
                    echo "<li>{$callout['regionName']} ({$callout['superRegionName']})</li>";
                }
                echo "</ul>";
                break;
            case 'skin':
                echo "<h2>Levels:</h2>";
                echo "<ul>";
                foreach ($itemData['levels'] as $level) {
                    echo "<li>{$level['displayName']}</li>";
                }
                echo "</ul>";
                // ... (Display chromas if applicable)
                break;
        }
        ?>
    </div>

</body>
</html>
