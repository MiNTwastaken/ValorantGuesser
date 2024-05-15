<!DOCTYPE html>
<html>
<head>
  <title>Valorant Guesser Admin Panel</title>
  <link rel="stylesheet" href="styless.css">
</head>
<body>
  <?php
  session_start();

  if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== 1) {
    header("Location: login.php");
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
  <h1>Valorant Guesser Admin Panel</h1>
  <p>Welcome, <?php echo $_SESSION["username"];?>. Manage everything here</p>

  <form method="post" action="show_data.php">
    <select name="data_type">
      <option value="ability">Abilities</option>
      <option value="agent">Agents</option>
      <option value="graffiti">Graffiti</option>
      <option value="playercard">Player Cards</option>
      <option value="quote">Quotes</option>
      <option value="weapon">Weapons</option>
    </select>
    <button type="submit">Show Data</button>
  </form>

  <a href="login.php">Logout</a>

</body>
</html>
