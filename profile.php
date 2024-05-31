<?php
session_start();

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get user information based on username from session
$username = $_SESSION["username"];
$sql = "SELECT u.*, l.name AS level_name, l.total_exp AS next_level_exp 
        FROM user u 
        JOIN levels l ON u.lvl = l.id 
        WHERE username = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    // Format joined_at for display
    $joinedAtFormatted = date("F j, Y", strtotime($user['joined_at']));
} else {
    // Handle case where user is not found
    header("Location: login.php"); // Or display an error message
    exit();
}
mysqli_stmt_close($stmt);

// Handle edit mode using a hidden form field
$editing = isset($_POST['edit_mode']) && $_POST['edit_mode'] === 'true';

// Update Profile (remove username update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $editing = false; // Exit edit mode after submitting

    // Handle profile picture upload
    $newPicture = $user['picture']; // Initialize with existing picture
    if (!empty($_FILES["picture"]["name"])) {
        $targetDir = "img/profile/";
        $targetFile = $targetDir . basename($_FILES["picture"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["picture"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                $newPicture = $targetFile; // Update with new picture path
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    // Sanitize form data
    $newFavorite = mysqli_real_escape_string($connection, $_POST["favorite"]);
    $newEmail = mysqli_real_escape_string($connection, $_POST["email"]);
    
    // Prepare and execute the SQL update statement (without username update)
    $stmt = mysqli_prepare($connection, "UPDATE user SET picture = ?, favorite = ?, email = ? WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "ssss", $newPicture, $newFavorite, $newEmail, $username); 

    if (mysqli_stmt_execute($stmt)) {
        // Update session email
        $_SESSION['email'] = $newEmail;

        // After updating, redirect to profile.php
        header("Location: profile.php");
        exit(); 
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
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

<div class="content profile-container">  
    <h2>Profile</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="edit_mode" value="<?php echo $editing ? 'true' : 'false'; ?>">
        <div class="profile-info">
            <div class="profile-picture">
                <img src="<?php echo $user['picture']; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
                <?php if ($editing): ?>
                    <input type="file" name="picture" id="picture">
                <?php endif; ?>
            </div>
            <div class="user-details">
                <ul>
                    <li>Username: <?php echo htmlspecialchars($user['username']); ?></li> <li>Email: 
                        <?php if ($editing): ?>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        <?php else: echo htmlspecialchars($user['email']); endif; ?></li>
                    <li>Favorite Agent: 
                        <?php if ($editing): ?>
                            <select name="favorite" id="favoriteAgent"></select> 
                        <?php else: echo htmlspecialchars($user['favorite']); endif; ?></li>
                    <li>Level: <?php echo htmlspecialchars($user['level_name']); ?></li> 
                    <li>EXP: <?php echo $user['exp']; ?> / <?php echo $user['next_level_exp']; ?> (Next Level)</li>
                    <div class="exp-bar">
                        <div style="width: <?php echo ($user['exp'] / $user['next_level_exp']) * 100; ?>%;"></div>
                    </div>
                    <li>Joined at: <?php echo $joinedAtFormatted; ?></li>
                </ul>
            </div>
        </div>
        <?php if (!$editing): ?> <button type="submit" name="edit_mode" value="true">Edit</button>
        <?php else: ?> <button type="submit" name="submit">Update Profile</button> <?php endif; ?>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
        const favoriteAgentSelect = document.getElementById('favoriteAgent');

        // Fetch agent data from the API
        fetch('https://valorant-api.com/v1/agents')
            .then(response => response.json())
            .then(data => {
                data.data.forEach(agent => {
                    if (agent.isPlayableCharacter) {
                        const option = document.createElement('option');
                        option.value = agent.displayName;
                        option.text = agent.displayName;
                        // Set the selected attribute if it matches the user's favorite agent
                        if (agent.displayName === "<?php echo $user['favorite']; ?>") {
                            option.selected = true;
                        }
                        favoriteAgentSelect.add(option);
                    }
                });
            });
    });
</script>

</body>
</html>
