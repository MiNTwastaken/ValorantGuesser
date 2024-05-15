<!DOCTYPE html>
<html>
<head>
  <title>Valorant Forum - Post Details</title>
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

  // Get post ID from URL parameter
  $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

  // Check if post ID is valid
  if (!$postId) {
    echo "Invalid post ID.";
    exit;
  }

  // Function to get post details by ID
  function getpostDetails($connection, $postId) {
    $sql = "SELECT * FROM posts WHERE id = $postId";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) == 1) {
      return mysqli_fetch_assoc($result);
    } else {
      return false;
    }
  }

  // Get post details
  $post = getpostDetails($connection, $postId);

  // Check if post exists
  if (!$post) {
    echo "post not found.";
    exit;
  }

  // Function to get comments for a post
  function getComments($connection, $postId) {
    $sql = "SELECT c.content, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = $postId ORDER BY c.created_at ASC";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
      return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
      return false;
    }
  }

  // Get comments for this post
  $comments = getComments($connection, $postId);
  ?>

  <h1><?php echo $post['title']; ?></h1>

  <p>Created by: <?php echo $post['created_by']; ?></p>
  <p><?php echo $post['content']; ?></p>

  <?php
  // Check if user is post owner (compare usernames from session and post)
  $ispostOwner = $isLoggedIn && $post['created_by'] == $_SESSION["username"];
  ?>

  <?php if ($ispostOwner) : ?>
    <h2>Edit post</h2>
    <form action="edit_post.php" method="post">
      <input type="hidden" name="id" value="<?php echo $post['id']; ?>">  <label for="title">post Title:</label>
      <input type="text" name="title" id="title" value="<?php echo $post['title']; ?>" required>
      <label for="content">Content:</label>
      <textarea name="content" id="content" required><?php echo $post['content']; ?></textarea>
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
      <input type="hidden" name="post_id" value="<?php echo $postId; ?>">  
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