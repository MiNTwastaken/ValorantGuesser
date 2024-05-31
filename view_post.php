<?php
session_start();

// Database connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get post ID from query string
if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Fetch post details from the database
    $stmt = mysqli_prepare($connection, "SELECT * FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    $postResult = mysqli_stmt_get_result($stmt);

    if ($postRow = mysqli_fetch_assoc($postResult)) {
        $postTitle = $postRow['title'];
        $postContent = $postRow['content'];
        $createdBy = $postRow['created_by'];
        $createdAt = $postRow['created_at'];
    } else {
        // Handle case where post doesn't exist
        echo "Post not found.";
        exit();
    }
    // Fetch media files for the post
    $mediaFiles = [];
    $stmt = mysqli_prepare($connection, "SELECT media FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    $mediaResult = mysqli_stmt_get_result($stmt);
    if ($mediaRow = mysqli_fetch_assoc($mediaResult)) {
        $mediaFiles = explode(',', $mediaRow['media']);
    }

    // Fetch comments for the post
    $stmt = mysqli_prepare($connection, "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    $commentsResult = mysqli_stmt_get_result($stmt);

    $comments = mysqli_fetch_all($commentsResult, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt); // Close prepared statement for fetching
} else {
    // Handle case where no post ID is provided
    echo "Invalid request.";
    exit();
}

// Handle comment submission (only if user is logged in)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"])) {
    $commentText = htmlspecialchars($_POST["comment_text"]);

    if (!empty($commentText)) {
        // Insert comment into database
        $stmt = mysqli_prepare($connection, "INSERT INTO comments (post_id, commenter, comment_text) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $postId, $_SESSION["username"], $commentText);
        if (mysqli_stmt_execute($stmt)) {
            // Refresh the page to show the new comment
            header("Location: view_post.php?id=$postId");
            exit();
        } else {
            echo "Error inserting comment: " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt); // Close prepared statement for inserting comment
    }
}

// Close the connection
mysqli_close($connection);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Post</title>
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
        <div class="content">
            <h2><?php echo $postTitle; ?></h2>
            <p><?php echo $postContent; ?></p>

            <?php if (!empty($mediaFiles)): ?> 
                <div class="post-media" data-current-media-index="0">
                <?php foreach ($mediaFiles as $index => $mediaFile) : ?>
                    <?php
                        $filePath = "content/" . $mediaFile;
                        $fileType = mime_content_type($filePath);
                        $display = ($index === 0) ? 'block' : 'none'; 
                    ?>
                    <div class="media-item" style="display: <?= $display ?>;">
                        <?php if (strpos($fileType, 'image/') === 0) : ?>
                            <img src="<?= $filePath ?>" alt="Post Media" class="file-preview">
                        <?php elseif (strpos($fileType, 'video/') === 0) : ?>
                            <video src="<?= $filePath ?>" controls class="file-preview"></video>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                    
                    <div class="media-controls" style="display: <?php echo (count($mediaFiles) > 1) ? 'flex' : 'none'; ?>;">
                        <button class="media-prev" onclick="showPrevMedia(<?php echo $postId; ?>)">&lt;</button>
                        <button class="media-next" onclick="showNextMedia(<?php echo $postId; ?>)">&gt;</button>
                    </div>
                </div>
            <?php endif; ?>


            <h3>Comments</h3>

            <?php if (isset($_SESSION["username"])): ?>
                <form action="" method="post">
                    <textarea name="comment_text" placeholder="Write your comment..."></textarea>
                    <button type="submit">Submit Comment</button>
                </form>
            <?php else: ?>
                <p>You need to be logged in to comment.</p>
            <?php endif; ?>
            <ul class="comment-list">
                <?php foreach ($comments as $comment): ?>
                    <li>
                        <strong><?php echo $comment['commenter']; ?></strong> (<?php echo $comment['created_at']; ?>):
                        <p><?php echo $comment['comment_text']; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <script>
        // Show Previous Media Function
        function showPrevMedia(postId) {
            const postMediaDiv = document.querySelector(`.post-media[data-current-media-index]`); // Select by the data attribute
            let currentMediaIndex = parseInt(postMediaDiv.dataset.currentMediaIndex);
            const mediaItems = postMediaDiv.querySelectorAll('.media-item'); 

            mediaItems[currentMediaIndex].style.display = 'none';
            currentMediaIndex = (currentMediaIndex - 1 + mediaItems.length) % mediaItems.length;
            postMediaDiv.dataset.currentMediaIndex = currentMediaIndex;
            mediaItems[currentMediaIndex].style.display = 'block';
        }

        // Show Next Media Function
        function showNextMedia(postId) {
            const postMediaDiv = document.querySelector(`.post-media[data-current-media-index]`); // Select by the data attribute
            let currentMediaIndex = parseInt(postMediaDiv.dataset.currentMediaIndex);
            const mediaItems = postMediaDiv.querySelectorAll('.media-item'); 

            mediaItems[currentMediaIndex].style.display = 'none';
            currentMediaIndex = (currentMediaIndex + 1) % mediaItems.length;
            postMediaDiv.dataset.currentMediaIndex = currentMediaIndex; 
            mediaItems[currentMediaIndex].style.display = 'block';
        }
    </script>
</body>
</html>
