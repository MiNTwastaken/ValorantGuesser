<!DOCTYPE html>
<html>
<head>
  <title>Valorant Forum - Profile</title>
  <link rel="stylesheet" href="styless.css">
</head>
<body>


<?php
// Start session (if not already started)
session_start();

// Check if user is logged in, redirect if not
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Connect to database (replace with your connection details)
$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get user information based on username from session
$username = $_SESSION["username"];
$sql = "SELECT * FROM user WHERE username='$username'";
$result = mysqli_query($connection, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result); // Get user data as associative array

    // Format joined_at for display (adjust format as needed)
    $joinedAtFormatted = date("F j, Y", strtotime($user['joined_at']));
} else {
    echo "Error: User information not found."; // Handle case where user info not found
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
  <div class="content profile-container">
    <h2>Profile</h2>

    <?php if (isset($user)) : ?>
      <div class="profile-info">
        <div class="profile-picture">
          <img src="<?php echo $user['picture']; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">  <a href="?edit=true" class="edit-btn">Edit</a>
        </div>
        <div class="user-details">
          <ul>
            <li>Username: <?php echo $user['username']; ?></li>
            <li>Email: <?php echo $user['email']; ?></li>
            <li>Favorite Agent: <?php echo $user['favorite']; ?></li>
            <li>Level: <?php echo $user['lvl']; ?> <?php if (isset($_SESSION["editing"])) : ?><a href="?edit=true" class="edit-btn">Edit (not possible)</a><?php endif; ?></li>
            <li>EXP: <?php echo $user['exp']; ?> / <?php echo $user['lvl'] * 10000; ?> (Next Level)</li>
            <div class="exp-bar">
              <div style="width: <?php echo ($user['exp'] / ($user['lvl'] * 10000)) * 100; ?>%"></div>
            </div>
            <li>Joined at: <?php echo $joinedAtFormatted; ?></li>
          </ul>
        </div>
      </div>

      <?php if (isset($_SESSION["editing"])) : ?>
        <?php endif; ?>

    <?php else : ?>
      <script>
        window.location.href = "login.php";
      </script>
    <?php endif; ?>

  </div>

  <script src="script.js"></script>
</body>
</html>
