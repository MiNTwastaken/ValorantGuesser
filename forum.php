<!DOCTYPE html>
<html>
<head>
  <title>Valorant Forums - Forum Details</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php
  // Start session (if not already started)
  session_start();

  // Check if user is logged in
  $isLoggedIn = isset($_SESSION["username"]);

  // Database Connection (replace with your connection details)
  $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
  if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
  }

  // Get forum ID from URL parameter
  $forumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

  // Check if forum ID is valid
  if (!$forumId) {
    echo "Invalid forum ID.";
    exit;
  }

  // Function to get forum details by ID
  function getForumDetails($connection, $forumId) {
    $sql = "SELECT * FROM forums WHERE id = $forumId";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) == 1) {
      return mysqli_fetch_assoc($result);
    } else {
      return false;
    }
  }

  // Get forum details
  $forum = getForumDetails($connection, $forumId);

  // Check if forum exists
  if (!$forum) {
    echo "Forum not found.";
    exit;
  }

  // Function to get comments for a forum
  function getComments($connection, $forumId) {
    $sql = "SELECT c.content, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.forum_id = $forumId ORDER BY c.created_at ASC";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
      return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
      return false;
    }
  }

  // Get comments for this forum
  $comments = getComments($connection, $forumId);
  ?>

  <h1><?php echo $forum['title']; ?></h1>

  <p>Created by: <?php echo $forum['created_by']; ?></p>
  <p><?php echo $forum['content']; ?></p>

  <?php
  // Check if user is forum owner (compare usernames from session and forum)
  $isForumOwner = $isLoggedIn && $forum['created_by'] == $_SESSION["username"];
  ?>

  <?php if ($isForumOwner) : ?>
    <h2>Edit Forum</h2>
    <form action="edit_forum.php" method="post">
      <input type="hidden" name="id" value="<?php echo $forum['id']; ?>">  <label for="title">Forum Title:</label>
      <input type="text" name="title" id="title" value="<?php echo $forum['title']; ?>" required>
      <label for="content">Content:</label>
      <textarea name="content" id="content" required><?php echo $forum['content']; ?></textarea>
      <button type="submit">Save Changes</button>
    </form>
  <?php endif; ?>

  <h2>Comments</h2>

  <?php if ($comments) : ?>
    <ul>
      <?php foreach ($comments as $comment) : ?>
        <li>
          <strong><?php echo $comment['username']; ?>:</strong> <?php echo $comment['content']; ?> (<?php echo date('Y-m-d H:i:s', strtotime($comment['created_at'])); ?>)
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else : ?>
    <p>No comments yet.</p>
  <?php endif; ?>

  <?php if ($isLoggedIn) : ?>
    <h2>Leave a Comment</h2>
    <form action="add_comment.php" method="post">
      <input type="hidden" name="forum_id" value="<?php echo $forumId; ?>">  
      <textarea name="content" id="content" placeholder="Write your comment here..." required></textarea>
      <button type="submit">Post Comment</button>
    </form>
  <?php else : ?>
    <p>Please log in to leave a comment.</p>
  <?php endif; ?>

  <?php
  // Close database connection
  mysqli_close($connection);
  ?>

</body>
</html>