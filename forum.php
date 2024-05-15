<!DOCTYPE html>
<html>
<head>
    <title>Valorant Forum</title>
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

    <div class="content">

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
            <div class="post-list">
                <?php foreach ($posts as $post) : ?>
                    <div class="post-box">
                        <h3 class="post-title"><?php echo $post['title']; ?></h3>
                        <p class="post-meta">Created by: <span class="post-author"><?php echo $post['created_by']; ?></span> on <span class="post-date"><?php echo $post['created_at']; ?></span></p>
                        <p class="post-content"><?php echo $post['content']; ?></p>
                        <a href="view_post.php?id=<?php echo $post['id']; ?>" class="read-more">Read More</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No posts created yet.</p>
        <?php endif; ?>

        <?php if ($isLoggedIn) : ?>
            <h2>Create a New Post</h2>
            <form action="create_post.php" method="post">
                <label for="title">Post Title:</label>
                <input type="text" name="title" id="title" required>
                <label for="content">Content:</label>
                <textarea name="content" id="content" required></textarea>
                <button type="submit">Create Post</button>
            </form>

        <?php else : ?>
            <p>To create posts, please log in or register.</p>
        <?php endif; ?>

        <?php
        // Close database connection
        mysqli_close($connection);
        ?>
    </div>
    <script src="script.js"></script>
</body>
</html>
