<!DOCTYPE html>
<html>
<head>
  <title>Valorant Guesser Admin Panel</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php
  session_start();

  if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== 1) {
    header("Location: login.php");
  }

  ?>

  <h1>Valorant Guesser Admin Panel</h1>
  <p>Welcome, <?php echo $_SESSION["username"]; ?></p>

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
