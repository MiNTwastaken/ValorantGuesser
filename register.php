<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer files

// Initialize variables
$usernameErr = $passwordErr = $emailErr = "";
$username = $password = $email = "";

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate username
  if (empty($_POST["username"])) {
    $usernameErr = "Username is required";
  } else {
    $username = htmlspecialchars($_POST["username"]);
  }

  // Validate email
if (empty($_POST["email"])) {
    $emailErr = "Email is required";
} else {
    $email = htmlspecialchars($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
    }
  }

// Validate password
if (empty($_POST["password"])) {
  $passwordErr = "Password is required";
} else {
  $password = htmlspecialchars($_POST["password"]);
}

$sql = "SELECT * FROM user WHERE username = '$username' OR email = '$email'";
$result = mysqli_query($connection, $sql);

if (mysqli_num_rows($result) > 0) {
    $usernameErr = "Username or email already exists";
} else {
  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Insert new user into database
  $sql = "INSERT INTO user (username, password, email, email_confirmed) VALUES ('$username', '$hashedPassword', '$email', 0)";

  if (mysqli_query($connection, $sql)) {
    // Email Confirmation using PHPMailer
    $mail = new PHPMailer(true); // Enable exceptions

    try {
      // Server settings
      $mail->SMTPDebug = 0; 
      $mail->isSMTP();                                            
      $mail->Host       = 'smtp.gmail.com';                    
      $mail->SMTPAuth   = true;                                   
      $mail->Username   = 'valorantforumnoreply@gmail.com';                     
      $mail->Password   = 'nuh uh github';                       
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
      $mail->Port       = 465;                                    

      // Recipients
      $mail->setFrom('valorantforumnoreply@gmail.com', 'Valorant Fanpage');
      $mail->addAddress($email, $username);

      // Content
      $confirmationLink = "http://localhost/ValorantGuesser/register_confirm.php?email=" . urlencode($email);

      $mail->isHTML(true);                                  
      $mail->Subject = 'Confirm your registration';
      $mail->Body    = "Hi $username,<br><br>Please click the following link to confirm your registration:<br><br><a href='$confirmationLink'>$confirmationLink</a>";
      $mail->AltBody = "Hi $username,\n\nPlease visit this link to confirm your registration:\n\n$confirmationLink";

      $mail->send();
      header("Location: register_confirm.php"); 
      exit;

    } catch (Exception $e) {
      // Error handling in case the email cannot be sent
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
  echo "Error: " . $sql . "<br>" . mysqli_error($connection);
}
}

// Close the connection
mysqli_close($connection);
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

<div class="logreg-container register-page">
  <h2>Valorant Fanpage Register</h2>
  
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" name="username" id="username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ""; ?>" required>
          <span class="error">* <?php echo $usernameErr; ?></span>
      </div>
  
      <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" name="email" id="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""; ?>" required>
          <span class="error">* <?php echo $emailErr; ?></span>
      </div>

      <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required>
          <span class="error">* <?php echo $passwordErr; ?></span>
      </div>
  
      <div class="form-group">
          <button type="submit" class="register-btn" name="register">Register</button>
      </div>
  </form>
</div>
<script src="script.js"></script>
</body>
</html>
