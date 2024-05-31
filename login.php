<?php
session_start();
// Initialize variables
$usernameErr = "";
$passwordErr = "";
$activationRequired = false;  // New flag for activation message

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["username"])) {
    $usernameErr = "Username is required";
  } else {
    $username = htmlspecialchars($_POST["username"]);
  }

  if (empty($_POST["password"])) {
    $passwordErr = "Password is required";
  } else {
    $password = htmlspecialchars($_POST["password"]);
  }

  // Validate credentials against database (for login)
  if (!empty($username) && !empty($password)) {
    // Set up database connection
    $connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");

    // Check connection
    if (!$connection) {
      die("Connection failed: " . mysqli_connect_error());
    }

      // Prepare and execute statement to prevent SQL injection
      $stmt = mysqli_prepare($connection, "SELECT * FROM user WHERE username = ?");
      mysqli_stmt_bind_param($stmt, "s", $username);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
          if ($row['email_confirmed'] == 1) { // Check if email is confirmed
            // Login successful, proceed
            $loginSuccess = true;
            session_start();
            $_SESSION["username"] = $username;

            if ($row["admin"] == 1) {
                $_SESSION["admin"] = 1;
                header("Location: admin.php"); // Redirect to admin panel
            } else {
                $_SESSION["admin"] = 0;
                header("Location: index.php"); 
            }
            exit;
            } else {
              // Email not confirmed, set flag
              $activationRequired = true;
            }
        } else {
          $passwordErr = "Invalid username or password";
        }
    } else {
      $usernameErr = "Invalid username or password";
    }
      
// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($connection);

  }
}
?>
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

  <div class="logreg-container login-page">
    <h2>Valorant Fanpage Login</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ""; ?>">
        <span class="error">* <?php echo $usernameErr; ?></span>
      </div>

      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
        <span class="error">* <?php echo $passwordErr; ?></span>
      </div>

      <?php if ($activationRequired): ?>
        <div class="error">* Please check your email for account activation.</div>
      <?php endif; ?>

      <div class="form-group">
        <button type="submit" name="login">Login</button>
        <button type="button" onclick="window.location.href='register.php'">Not a user? Register</button>
      </div>

    </form>
  </div>
</body>
</html>