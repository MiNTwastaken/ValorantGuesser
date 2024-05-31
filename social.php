<?php
session_start();

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to get all posts
function getAllPosts($connection) {
    $sql = "SELECT p.*, t.name AS tag_name 
            FROM posts p
            LEFT JOIN tags t ON p.tags_id = t.id 
            ORDER BY p.created_at DESC"; // Join with tags table to get tag name
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return false;
    }
}

// Get all posts
$posts = getAllPosts($connection);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Valorant Social</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        .drop-area {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px; 
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drop-text {
            display: block;
            width: 100%;
            height: 100%;
            text-align: center;
        }

        .highlight {
            border-color: #007bff; 
        }

        .file-preview {
            max-width: 150px; 
            max-height: 150px;
            margin: 5px;
        }

        .media-controls {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        #media {
            display: none; 
        }

        .media-item {
            position: relative;
        }

        .media-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #f00;
            color: #fff; 
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .file-preview {
            max-width: 100%; /* Allow them to take up full width */
            height: 150px;    /* Set a fixed height */
            object-fit: contain; /* Fit the content inside the container (might leave empty space)*/
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
        <?php if (isset($_SESSION["username"])): ?>
            <div class="create-post-container">
                <h2>Create a New Post</h2>
                <form action="create_post.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Post Title:</label>
                        <input type="text" name="title" id="title" required>
                    </div>

                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea name="content" id="content" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="media">Media (Max 3 files):</label>
                        <div class="drop-area" id="dropArea">
                            <input type="file" name="media[]" id="media" multiple accept="image/*,video/*" />
                            <span class="drop-text">Drag & drop files here or click to browse</span>
                        </div>
                        <div id="filePreviews"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tags_id">Tag:</label>
                        <select name="tags_id" id="tags">
                            <?php
                            // Fetch tags from the database
                            $tagsQuery = "SELECT * FROM tags";
                            $tagsResult = mysqli_query($connection, $tagsQuery);

                            while ($tag = mysqli_fetch_assoc($tagsResult)) {
                                echo "<option value='" . $tag['id'] . "'>" . $tag['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="create-post-btn">Create Post</button>
                </form>
            </div>
        <?php else : ?>
            <div class="login-prompt">
                <p>To create posts, please <a href="login.php">log in</a> or <a href="register.php">register</a>.</p>
            </div>
        <?php endif; ?>

        <h2>Current posts</h2>
        <?php if ($posts) : ?>
            <div class="post-list">
                <?php foreach ($posts as $post): ?>
                    <div class="post-box" data-post-id="<?php echo $post['id']; ?>">
                        <h3 class="post-title"><?php echo $post['title']; ?></h3>
                        <p class="post-meta">Created by: <span class="post-author"><?php echo $post['created_by']; ?></span> on <span class="post-date"><?php echo $post['created_at']; ?></span> - <span class="post-tags"><?php echo $post['tag_name'] ?? 'No tag'; ?></span> </p> 
                        <p class="post-content"><?php echo $post['content']; ?></p>
                        <div class="post-media" data-current-media-index="0"> <?php 
                            $mediaFiles = explode(',', $post['media']);
                            foreach($mediaFiles as $index => $mediaFile) : 
                                $filePath = "content/" . $mediaFile;
                                $fileType = mime_content_type($filePath);
                                $display = ($index === 0) ? 'block' : 'none'; // Display the first media item by default
                                ?>
                                <div class="media-item" style="display: <?= $display ?>;">
                                    <?php if (strpos($fileType, 'image/') === 0) : ?>
                                        <img src="<?= $filePath ?>" alt="Post Media" class="file-preview">
                                    <?php elseif (strpos($fileType, 'video/') === 0) : ?>
                                        <video src="<?= $filePath ?>" controls class="file-preview"></video>
                                    <?php else : ?>

                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="media-controls" style="display: <?php echo (count($mediaFiles) > 1) ? 'flex' : 'none'; ?>;">
                                <button class="media-prev" onclick="showPrevMedia(<?php echo $post['id']; ?>)">&lt;</button>
                                <button class="media-next" onclick="showNextMedia(<?php echo $post['id']; ?>)">&gt;</button>
                            </div>
                        </div>
                        <a href="view_post.php?id=<?php echo $post['id']; ?>" class="read-more">Read Comments</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No posts created yet.</p>
        <?php endif; ?>
    </div>
    <script>
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('media');
        const previewsContainer = document.getElementById('filePreviews');

        // Drag and Drop Event Listeners
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('highlight');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('highlight');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('highlight');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        // Click to browse for files (only when clicking directly on the drop area)
        dropArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        // Handle Files Function
        function handleFiles(files) {
            for (let i = 0; i < Math.min(files.length, 3); i++) { 
                const file = files[i];

                const mediaItem = document.createElement('div');
                mediaItem.classList.add('media-item');

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.classList.add('file-preview');
                    img.file = file; 
                    mediaItem.appendChild(img);

                    const reader = new FileReader();
                    reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.classList.add('file-preview');
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    mediaItem.appendChild(video);
                } else {
                    console.warn('Unsupported file type:', file.type);
                    continue; 
                }

                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'X';
                removeBtn.classList.add('remove-btn');
                removeBtn.onclick = function() {
                    previewsContainer.removeChild(mediaItem); 
                };
                mediaItem.appendChild(removeBtn);

                previewsContainer.appendChild(mediaItem);
            }
        }

        // Show Previous Media Function
        function showPrevMedia(postId) {
            const postMediaDiv = document.querySelector(`.post-box[data-post-id="${postId}"] .post-media`);
            let currentMediaIndex = parseInt(postMediaDiv.dataset.currentMediaIndex);
            const mediaItems = postMediaDiv.querySelectorAll('.media-item'); 

            mediaItems[currentMediaIndex].style.display = 'none';
            currentMediaIndex = (currentMediaIndex - 1 + mediaItems.length) % mediaItems.length;
            postMediaDiv.dataset.currentMediaIndex = currentMediaIndex; 
            mediaItems[currentMediaIndex].style.display = 'block';
        }

        // Show Next Media Function
        function showNextMedia(postId) {
            const postMediaDiv = document.querySelector(`.post-box[data-post-id="${postId}"] .post-media`);
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
