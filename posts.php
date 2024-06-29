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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40;
        }
        .seethrough {
            background-color: rgba(248, 249, 250, 0.9);
            padding: 2rem;
            border-radius: 0.5rem;
        }
        .media-container {
            max-width: 18.75rem; /* 300px */
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<script src="script.js"></script>
    <?php include 'navbar.php'; ?>
    <div class="container mt-sm-5 p-5 rounded-lg seethrough">
        <h2>Manage Posts</h2>
        
        <form method="get" action="" class="d-flex mb-3">
            <input type="text" name="search" class="form-control me-2" placeholder="Search posts" value="<?php echo $searchQuery; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div class="mb-3">
            <a href="?sort=title&order=<?php echo ($sortBy == 'title' && $sortOrder == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Title</a> |
            <a href="?sort=created_at&order=<?php echo ($sortBy == 'created_at' && $sortOrder == 'ASC') ? 'DESC' : 'ASC'; ?>">Sort by Date</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
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
                                            echo "<div class='media-container'><video controls class='w-100'><source src='$filePath' type='video/mp4'></video></div>";
                                        } else {
                                            echo "<div class='media-container'><img src='$filePath' alt='Post Media' class='img-fluid' /></div>";
                                        }
                                    }
                                }
                                ?>
                            </td>
                            <td><?php echo $post['created_by']; ?></td>
                            <td><?php echo $post['created_at']; ?></td>
                            <td><?php echo $post['tag_name'] ?? 'No tag'; ?></td>
                            <td>
                                <ul class="list-unstyled">
                                    <?php
                                    // Fetch comments for this post (prepared statement)
                                    $commentStmt = mysqli_prepare($connection, "SELECT * FROM comments WHERE post_id = ?");
                                    mysqli_stmt_bind_param($commentStmt, "i", $post['id']);
                                    mysqli_stmt_execute($commentStmt);
                                    $commentResult = mysqli_stmt_get_result($commentStmt);
                                    $comments = mysqli_fetch_all($commentResult, MYSQLI_ASSOC);
                                    mysqli_stmt_close($commentStmt);
                                    foreach ($comments as $comment): ?>
                                        <li class="border p-2 mb-2 rounded">
                                            <strong><?php echo $comment['commenter']; ?>:</strong> <?php echo $comment['comment_text']; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="delete_post" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?');">
                                    Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>