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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .media-item img, .media-item video {
            height: 33rem;
            width: 100%;
            display: block;
            margin: 0 auto;
        }
        .media-controls {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }
        .comment-list {
            list-style-type: none;
            padding-left: 0;
        }
        .container {
            max-width: 100%;
            padding: 15px;
        }
        .card {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .media-item {
            display: none;
        }
        .media-item:first-child {
            display: block;
        }
        .seethrough {
            background-color: rgba(248, 249, 250, 0.9);
        }
        @media (min-width: 768px) {
            .container {
                max-width: 50rem;
            }
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <?php include 'navbar.php'; ?>
        <div class="container mt-sm-5 p-3 p-sm-5 rounded-lg seethrough">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title"><?php echo $postTitle; ?></h2>
                    <p class="card-text"><?php echo $postContent; ?></p>
                </div>
            </div>

            <?php if (!empty($mediaFiles)): ?>
                <div class="post-media mb-4" data-current-media-index="0">
                    <?php foreach ($mediaFiles as $index => $mediaFile) : ?>
                        <?php
                            $filePath = "content/" . $mediaFile;
                            $fileType = mime_content_type($filePath);
                        ?>
                        <div class="media-item">
                            <?php if (strpos($fileType, 'image/') === 0) : ?>
                                <img src="<?= $filePath ?>" alt="Post Media" class="img-fluid rounded">
                            <?php elseif (strpos($fileType, 'video/') === 0) : ?>
                                <video src="<?= $filePath ?>" controls class="img-fluid rounded"></video>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($mediaFiles) > 1) : ?> <div class="media-controls">
                        <button class="btn btn-secondary" onclick="showPrevMedia()">&lt;</button>
                        <button class="btn btn-secondary" onclick="showNextMedia()">&gt;</button>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title">Comments</h3>

                <?php if (isset($_SESSION["username"])): ?>
                    <form action="" method="post" class="mb-4">
                        <div class="mb-3">
                            <textarea name="comment_text" class="form-control" placeholder="Write your comment..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Comment</button>
                    </form>
                <?php else: ?>
                    <p>You need to be logged in to comment.</p>
                <?php endif; ?>

                <ul class="list-group">
                    <?php foreach ($comments as $comment): ?>
                        <li class="list-group-item">
                            <strong><?php echo $comment['commenter']; ?></strong> (<?php echo $comment['created_at']; ?>):
                            <p><?php echo $comment['comment_text']; ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            </div>
        </div>
    <script>
        // Show Previous Media Function
        function showPrevMedia() {
            const postMediaDiv = document.querySelector(`.post-media[data-current-media-index]`); // Select by the data attribute
            let currentMediaIndex = parseInt(postMediaDiv.dataset.currentMediaIndex);
            const mediaItems = postMediaDiv.querySelectorAll('.media-item'); 

            mediaItems[currentMediaIndex].style.display = 'none';
            currentMediaIndex = (currentMediaIndex - 1 + mediaItems.length) % mediaItems.length;
            postMediaDiv.dataset.currentMediaIndex = currentMediaIndex;
            mediaItems[currentMediaIndex].style.display = 'block';
        }

        // Show Next Media Function
        function showNextMedia() {
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
