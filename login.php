<?php
// Initialize variables
$usernameErr = "";
$passwordErr = "";
$registerSuccess = false; // Flag for successful registration

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
    $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

    // Check connection
    if (!$connection) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      if (password_verify($password, $row['password'])) {
        // Password is valid, login successful

        $loginSuccess = true;
        session_start();
        $_SESSION["username"] = $username;

        if ($row["admin"] == 1) {
          $_SESSION["admin"] = 1;
          header("Location: admin.php"); // Redirect to admin panel if admin
        } else {
          $_SESSION["admin"] = 0;
          header("Location: index.php"); // Redirect to non-admin page otherwise
        }

        exit;
      } else {
        $passwordErr = "Invalid password";
      }
    } else {
      $usernameErr = "Invalid username";
    }

    // Close the connection
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
            session_start();
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

    <div class="form-group">
      <button type="submit" name="login">Login</button>
      <button type="button" onclick="window.location.href='register.php'">Not a user? Register</button>
    </div>

  </form>
</div>

<script src="script.js"></script>
</body>
</html>