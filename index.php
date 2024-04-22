<!DOCTYPE html>
<html>
<head>
  <title>Valorant post</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Valorant Forum</h1>

  <?php
  // Start session (if not already started)
  session_start();

  // Check if user is logged in
  $isLoggedIn = isset($_SESSION["username"]);

  if ($isLoggedIn) {
    echo "<h2>Welcome back, " . $_SESSION["username"] . "!</h2>";
  }
  ?>

  <?php
  // Database Connection (replace with your connection details)
  $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
  if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
  }

  // Function to get all posts
  function getAllposts($connection) {
    $sql = "SELECT * FROM posts ORDER BY created_at DESC";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
      return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
      return false;
    }
  }

  // Get all posts
  $posts = getAllposts($connection);
  ?>

  <h2>Current posts</h2>

  <?php if ($posts) : ?>
    <ul>
		<?php foreach ($posts as $post) : ?>
		<li>
			<h2><?php echo $post['title']; ?></h2>
			(Created by: <?php echo $post['created_by']; ?>)<br>
			<?php
			// Shorten content with character limit and add ellipsis
			$contentSnippet = substr($post['content'], 0, 100) . (strlen($post['content']) > 100 ? "..." : "");
			echo $contentSnippet;
			?>
			<br>
			<a href="post.php?id=<?php echo $post['id']; ?>">Open post</a>
		</li>
      <?php endforeach; ?>
    </ul>
  <?php else : ?>
    <p>No posts created yet.</p>
  <?php endif; ?>

  <?php if ($isLoggedIn) : ?>
    <h2>Create a New post</h2>
    <form action="create_post.php" method="post">
      <label for="title">post Title:</label>
      <input type="text" name="title" id="title" required>
      <label for="content">Content:</label>
      <textarea name="content" id="content" required></textarea>
      <button type="submit">Create post</button>
    </form>

	<form action="logout.php" method="post">
      <button type="submit">Logout</button>
    </form>

  <?php else : ?>
    <p>To create posts, please log in or register.</p>
    <a href="login.php">Login/Register</a>
  <?php endif; ?>

  <p>Play a game</p>
  <form>
    <button formaction="daily.php"></button>
    <button formaction="oneshot.php"></button>
    <button formaction="freeplay.php"></button>
  </form>

  <?php
  // Close database connection
  mysqli_close($connection);
  ?>
</body>
</html>
