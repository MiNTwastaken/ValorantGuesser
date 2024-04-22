<!DOCTYPE html>
<html>
<head>
  <title>Valorant Forum Registration</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
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
          $registerSuccess = true;
          echo "<h2>Registration successful!</h2>";
        } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($connection);
        }
      }

      mysqli_close($connection);
    }
  }
  ?>

  <h2>Valorant Forum Registration</h2>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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

</body>
</html>
