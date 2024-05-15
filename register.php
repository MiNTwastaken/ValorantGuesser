
<?php
// Initialize variables
$usernameRegErr = "";
$passwordRegErr = "";
$registerSuccess = false;

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
  if (empty($_POST["username"])) {
    $usernameRegErr = "Username is required";
  } else {
    $username = htmlspecialchars($_POST["username"]);
  }

  if (empty($_POST["password"])) {
    $passwordRegErr = "Password is required";
  } else {
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);  // Hash password before storing
  }

  // Validate username uniqueness (check if username exists)
  if (!empty($username) && !empty($password)) {
    $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

    if (!$connection) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) > 0) {
      $usernameRegErr = "Username already exists";
    } else {
      // Insert new user into database
      $sql = "INSERT INTO user (username, password) VALUES ('$username', '$password')";

      if (mysqli_query($connection, $sql)) {
        // Registration successful
        header("Location: login.php"); // Redirect to login.php
        exit; // Stop further execution of this script
      } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($connection);
      }
    }

    mysqli_close($connection);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Valorant Fanpage Registration</title>
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



<div class="logreg-container register-page">
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<h2>Valorant Fanpage Registration</h2>
  <div class="form-group">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ""; ?>">
    <span class="error">* <?php echo $usernameRegErr; ?></span>
  </div>
  <div class="form-group">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password">
    <span class="error">* <?php echo $passwordRegErr; ?></span>
  </div>
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <span class="error">* <?php echo isset($emailErr) ? $emailErr : ""; ?></span>
  </div>
  <div class="form-group">
    <button type="submit" name="register">Register</button>
  </div>
</form>
</div>
<script src="script.js"></script>
</body>
</html>
