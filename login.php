<!DOCTYPE html>
<html>
<head>
  <title>Valorant Guesser Admin Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php
  // Initialize variables
  $usernameErr = "";
  $passwordErr = "";
  $loginSuccess = false;

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

    // Validate credentials against database
    if (!empty($username) && !empty($password)) {
      // Set up database connection
      $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

      // Check connection
      if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
      }

      $sql = "SELECT * FROM admin WHERE username = '$username'";
      $result = mysqli_query($connection, $sql);

      if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($password == $row['password']) { // Currently I am not using Hashing
          $loginSuccess = true;
          session_start();
          $_SESSION["username"] = $username;
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

  <h2>Valorant Guesser Admin Login</h2>
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
    </div>
  </form>

  <?php
  if ($loginSuccess) {
    header("Location: admin.php");
  }
  ?>

</body>
</html>