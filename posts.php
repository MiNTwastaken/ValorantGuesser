<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || (isset($_SESSION["admin"]) && $_SESSION["admin"] != 1)) {
    header("Location: login.php");
    exit();
}

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");

// Handle search and sort parameters
$searchQuery = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? mysqli_real_escape_string($connection, $_GET['sort']) : 'created_at';
$sortOrder = isset($_GET['order']) ? mysqli_real_escape_string($connection, $_GET['order']) : 'DESC';

// Build the SQL query (include tags in the query)
$sql = "SELECT p.*, t.name AS tag_name
        FROM posts p
        LEFT JOIN tags t ON p.tags_id = t.id 
        WHERE 1=1";

if (!empty($searchQuery)) {
    $sql .= " AND (title LIKE '%$searchQuery%' OR content LIKE '%$searchQuery%')";
}

$sql .= " ORDER BY $sortBy $sortOrder";

// Execute the query
$result = mysqli_query($connection, $sql);
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle post deletion
if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
    $deletePostId = $_POST['post_id'];

    // Delete comments associated with the post first
    $deleteCommentsStmt = mysqli_prepare($connection, "DELETE FROM comments WHERE post_id = ?");
    mysqli_stmt_bind_param($deleteCommentsStmt, "i", $deletePostId);
    mysqli_stmt_execute($deleteCommentsStmt);

    // Delete the post
    $deletePostStmt = mysqli_prepare($connection, "DELETE FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($deletePostStmt, "i", $deletePostId);
    mysqli_stmt_execute($deletePostStmt);

    // Redirect back to the posts.php page
    header("Location: posts.php");
    exit();
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Posts</title>
    <link rel="stylesheet" href="styless.css">
    <style>

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top; 
        }

        th {
            background-color: #f2f2f2; 
        }

        .content {
            padding: 20px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="text"] {
            padding: 8px;
            margin-right: 10px; 
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .sort-links a {
            color: #333; 
            text-decoration: none;
        }

        .sort-links a:hover {
            text-decoration: underline;
        }

        .media-container {
            max-width: 300px; 
            margin-bottom: 10px;
        }

        .comment-list {
            list-style: none;
            padding: 0;
        }

        .comment-list li {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
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
        <h2>Manage Posts</h2>
        
        <form method="get" action="">
            <input type="text" name="search" placeholder="Search posts" value="<?php echo $searchQuery; ?>">
            <button type="submit">Search</button>
        </form>

        <div class="sort-links">
            <a href="?sort=title&order=<?php echo ($sortBy == 'title' && $sortOrder == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Title</a> |
            <a href="?sort=created_at&order=<?php echo ($sortBy == 'created_at' && $sortOrder == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Date</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Media</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Tag</th>
                    <th>Comments</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo $post['title']; ?></td>
                        <td><?php echo $post['content']; ?></td>
                        <td>
                            <?php 
                            $mediaFiles = explode(",", $post['media']);
                            foreach($mediaFiles as $mediaFile) {
                                if (!empty($mediaFile)) {
                                    $filePath = "content/" . $mediaFile; 
                                    if (strpos($mediaFile, ".mp4") !== false || strpos($mediaFile, ".webm") !== false) {
                                        echo "<video controls width='200' height='150'><source src='$filePath' type='video/mp4'></video>";
                                    } else {
                                        echo "<img src='$filePath' alt='Post Media' width='200' height='150' />";
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $post['created_by']; ?></td>
                        <td><?php echo $post['created_at']; ?></td>
                        <td><?php echo $post['tag_name'] ?? 'No tag'; ?></td>
                        <td>
                            <ul class="comment-list">
                                <?php
                                // Fetch comments for this post (prepared statement)
                                $commentStmt = mysqli_prepare($connection, "SELECT * FROM comments WHERE post_id = ?");
                                mysqli_stmt_bind_param($commentStmt, "i", $post['id']);
                                mysqli_stmt_execute($commentStmt);
                                $commentResult = mysqli_stmt_get_result($commentStmt);
                                $comments = mysqli_fetch_all($commentResult, MYSQLI_ASSOC);
                                mysqli_stmt_close($commentStmt);
                                foreach ($comments as $comment): ?>
                                    <li>
                                        <strong><?php echo $comment['commenter']; ?>:</strong> <?php echo $comment['comment_text']; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="delete_post" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?');">
                                Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
